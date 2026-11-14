<?php

namespace App\Http\Controllers;

use App\Http\Requests\catatWaktuMasuk;
use App\Http\Requests\StoreKendaraan;
use App\Http\Requests\UpdateParkirRequest;
use App\Models\DataParkir;
use App\Models\Kendaraan;
use App\Models\nyubeUser;
use App\Models\PropertyAplikasi;
use App\Models\Tipe_kendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Midtrans\Config;

use Illuminate\Http\Request;

class catatWaktuParkirController extends Controller
{
    use ApiResponse;


    public function storeWaktuMasuk(catatWaktuMasuk $request)
    {
        $request->validated();
        $user = Auth::user();

        // Cek apakah user sudah punya parkir aktif dengan kode yang sama
        $existingParkir = DataParkir::where('user_id', $user->id)
            ->where('kode_parkir', $request->kode_parkir)
            ->first();

        if ($existingParkir) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data parkir dengan kode ini sudah ada.',
            ], 400);
        }

        // ✅ Generate dan simpan order_id Midtrans
        $generatedOrderId = 'PARKIR-' . $request->kode_parkir . '-' . Str::random(6);

        // Simpan data parkir masuk
        $parkir = new DataParkir();
        $parkir->user_id = $request->user_id;
        $parkir->kode_parkir = $request->kode_parkir;
        $parkir->id_merk_kendaraan = $request->id_merk_kendaraan;
        $parkir->id_plat_nomor = $request->id_plat_nomor;
        $parkir->id_tipe_kendaraan = $request->id_tipe_kendaraan;
        $parkir->id_warna_kendaraan = $request->id_warna_kendaraan;
        $parkir->waktu_masuk = now();
        $parkir->id_tarif = $request->id_tarif;
        $parkir->lokasi = $request->lokasi;
        $parkir->tempat = $request->tempat;
        $parkir->status = 'Belum Dibayar';

        if ($request->filled('waktu_keluar')) {
            $parkir->waktu_keluar = Carbon::parse($request->waktu_keluar)->format('Y-m-d H:i:s');
        }

        if ($request->filled('total')) {
            $parkir->total = (int) $request->total;
        }

        $parkir->save();

        // 🔁 Buat Snap Token Midtrans jika total dikirim (opsional)
        $snapToken = null;

        if ($request->filled('total')) {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $generatedOrderId,
                    'gross_amount' => (int) $request->total,
                ],
                'customer_details' => [
                    'first_name' => $user->name ?? 'User Parkir',
                    'email' => $user->email ?? 'noemail@domain.com',
                ],
                'item_details' => [
                    [
                        'id' => 'BIAYA_PARKIR',
                        'price' => (int) $request->total,
                        'quantity' => 1,
                        'name' => 'Biaya Parkir',
                    ]
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Gagal membuat Snap Token Midtrans.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data parkir berhasil dicatat.',
            'data' => $parkir,
            'midtrans_snap_token' => $snapToken,
        ], 201);
    }



    public function updateParkirByKode(UpdateParkirRequest $request, $kode_parkir)
    {
        $parkir = DataParkir::with('user')->where('kode_parkir', $kode_parkir)->first();

        if (!$parkir) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data parkir tidak ditemukan.',
            ], 404);
        }

        // Update total biaya
        $parkir->total = (int) $request->total;

        // Update field lain yang diizinkan
        $fields = [
            'id_merk_kendaraan',
            'id_plat_nomor',
            'id_tipe_kendaraan',
            'id_warna_kendaraan',
            'id_tarif',
            'lokasi',
            'waktu_keluar', // ← tambahkan ini!
        ];


        foreach ($fields as $field) {
            if ($request->filled($field)) {
                $parkir->{$field} = $request->{$field};
            }
        }

        $parkir->save();

        // Ambil user terkait
        $user = $parkir->user;

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User tidak ditemukan untuk data parkir ini.',
            ], 404);
        }

        // Cek saldo cukup atau tidak
        if ($user->saldo < $parkir->total) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Saldo tidak mencukupi.',
                'saldo_user' => $user->saldo,
                'total_parkir' => $parkir->total
            ], 400);
        }

        // Jika saldo cukup → lakukan pemotongan
        $user->saldo -= $parkir->total;
        $user->save();

        // Update status parkir misalnya “lunas”
        $parkir->status = 'Sudah Dibayar';
        $parkir->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data parkir berhasil diperbarui dan pembayaran menggunakan saldo berhasil.',
            'data' => [
                'parkir' => $parkir,
                'saldo_user_setelah_pembayaran' => $user->saldo
            ]
        ]);
    }




    public function generateKodeParkir()
    {
        $property = PropertyAplikasi::first();
        $prefix = $property ? $property->kode_parkir : 'PARKIR ';

        $count = 0;
        do {
            $kode = $prefix . ' ' . str_pad($count, 2, '0', STR_PAD_LEFT);
            $exists = DataParkir::where('kode_parkir', $kode)->exists();
            $count++;
        } while ($exists);

        return $kode;
    }


    public function showTarif($id)
    {
        // Ambil data berdasarkan ID
        $tipeKendaraan = Tipe_kendaraan::find($id);

        // Cek jika tidak ditemukan
        if (!$tipeKendaraan) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // Transformasi data langsung karena ini satu objek
        $data = [
            'id' => $tipeKendaraan->id,
            'nama_kendaraan' => $tipeKendaraan->nama_kendaraan,
            'tarif_parkir' => $tipeKendaraan->tarif_parkir,
            'keterangan_parkir' => $tipeKendaraan->keterangan_parkir,
        ];

        return $this->successResponse([
            'data' => $data,
        ], 'Berhasil mendapatkan data.');
    }






    public function show()
    {
        $properties = PropertyAplikasi::all();

        if ($properties->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        $data = $properties->map(function ($item) {
            return [
                'nama_aplikasi'     => $item->nama_aplikasi,
                'kode_parkir'       => $item->kode_parkir,
                'logo_aplikasi'     => $item->logo_aplikasi,
                'owner_parkir'      => $item->owner_parkir,
                'alamat_perusahaan' => $item->alamat_perusahaan,
                'email_perusahaan'  => $item->email_perusahaan,
                'no_perusahaan'     => $item->no_perusahaan,
            ];
        });

        return $this->successResponse([
            'data' => $data,
        ], 'Berhasil mendapatkan data.');
    }

    public function showHistory($user_id)
    {
        $dataParkir = DataParkir::with([
            'merkKendaraan',
            'platNomor',
            'tipeKendaraan.jenis',
            'warnaKendaraan',
            'tarif'
        ])
            ->where('user_id', $user_id) // filter berdasarkan user
            ->orderByDesc('created_at')
            ->take(2)
            ->get()
            ->reverse()
            ->values();

        if ($dataParkir->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        $data = $dataParkir->map(function ($item) {
            return [
                'kode_parkir'     => $item->kode_parkir,
                'waktu_masuk'     => $item->waktu_masuk,
                'lokasi'          => $item->lokasi,
                'status'          => $item->status,

                // Relasi
                'merk_kendaraan'  => optional($item->merkKendaraan)->merk_kendaraan ?? '-',
                'plat_nomor'      => optional($item->platNomor)->plat_nomor ?? '-',
                'tipe_kendaraan'  => optional($item->tipeKendaraan->jenis)->nama_kendaraan ?? '-',
                'warna_kendaraan' => optional($item->warnaKendaraan)->warna ?? '-',
                'tarif'           => optional($item->tarif)->tarif_parkir ?? '-',
            ];
        });

        return $this->successResponse([
            'data' => $data
        ], 'Berhasil mendapatkan data.');
    }



    public function showHistoryAll($user_id, Request $request)
    {
        $tanggal = $request->input('tanggal'); // contoh: 2025-07-02
        $kendaraan = $request->input('plat_nomor');

        $query = DataParkir::with([
            'merkKendaraan',
            'platNomor',
            'tipeKendaraan.jenis',
            'warnaKendaraan',
            'tarif'
        ])
            ->where('status', 'Sudah Dibayar')
            ->where('user_id', $user_id);

        // Filter berdasarkan tanggal (LIKE agar fleksibel)
        if ($tanggal) {
            $query->where('waktu_masuk', 'like', "$tanggal%");
        }

        // Filter berdasarkan plat nomor kendaraan
        if ($kendaraan) {
            $query->whereHas('platNomor', function ($q) use ($kendaraan) {
                $q->where('plat_nomor', $kendaraan);
            });
        }

        // ✅ Pagination 5 per halaman
        $dataParkir = $query->orderByDesc('created_at')->paginate(5);

        // Cek jika tidak ada data
        if ($dataParkir->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // ✅ Transformasi data di dalam koleksi paginate()
        $dataParkir->getCollection()->transform(function ($item) {
            $waktuMasuk = \Carbon\Carbon::parse($item->waktu_masuk);
            $waktuKeluar = $item->waktu_keluar ? \Carbon\Carbon::parse($item->waktu_keluar) : null;

            $durasi = $waktuKeluar
                ? $waktuMasuk->diff($waktuKeluar)->format('%h jam %i menit')
                : '-';

            return [
                'kode_parkir'     => $item->kode_parkir,
                'waktu_masuk'     => $item->waktu_masuk,
                'waktu_keluar'    => $item->waktu_keluar,
                'durasi'          => $durasi,
                'lokasi'          => $item->lokasi,
                'status'          => $item->status,
                'merk_kendaraan'  => optional($item->merkKendaraan)->merk_kendaraan ?? '-',
                'plat_nomor'      => optional($item->platNomor)->plat_nomor ?? '-',
                'tipe_kendaraan'  => optional($item->tipeKendaraan->jenis)->nama_kendaraan ?? '-',
                'warna_kendaraan' => optional($item->warnaKendaraan)->warna ?? '-',
                'tarif'           => optional($item->tarif)->tarif_parkir ?? '-',
            ];
        });

        // ✅ Kembalikan struktur pagination Laravel lengkap
        return $this->successResponse($dataParkir, 'Berhasil mendapatkan data.');
    }



    public function showParkirMasuk()
    {
        $dataParkir = DataParkir::with(['merkKendaraan', 'platNomor', 'tipeKendaraan', 'warnaKendaraan', 'tarif'])
            ->whereNull('waktu_keluar') // Data yang belum keluar
            ->latest('waktu_masuk')     // Urutkan berdasarkan waktu masuk, terbaru dulu
            ->first();                  // Ambil satu data saja

        if (!$dataParkir) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }
        $data = [
            'kode_parkir'     => $dataParkir->kode_parkir,
            'waktu_masuk' => $dataParkir->waktu_masuk,

            'lokasi'          => $dataParkir->lokasi,
            'status'          => $dataParkir->status,

            // Relasi
            'merk_kendaraan'  => optional($dataParkir->merkKendaraan)->merk_kendaraan ?? '-',
            'plat_nomor'      => optional($dataParkir->platNomor)->plat_nomor ?? '-',
            'tipe_kendaraan'  => optional($dataParkir->tipeKendaraan)->jenis->nama_kendaraan ?? '-',
            'warna_kendaraan' => optional($dataParkir->warnaKendaraan)->warna ?? '-',
            'tarif'           => optional($dataParkir->tarif)->tarif_parkir ?? '-',
        ];

        return $this->successResponse([
            'data' => $data,
        ], 'Berhasil mendapatkan data parkir masuk terbaru.');
    }

    public function cekStatusParkir($kode_parkir)
    {
        $dataParkir = DataParkir::with([
            'merkKendaraan',
            'platNomor',
            'tipeKendaraan',
            'warnaKendaraan',
            'tarif'
        ])
            ->where('kode_parkir', $kode_parkir)
            ->first();

        if (!$dataParkir) {
            return $this->errorResponse('Data parkir tidak ditemukan.', 404);
        }

        // Tambahkan pengecekan status
        if ($dataParkir->status === 'Sudah Dibayar') {
            return $this->successResponse([
                'status' => 'Sudah Dibayar',
                'message' => 'Parkir sudah dibayar.',
                'data' => [
                    'kode_parkir' => $dataParkir->kode_parkir,
                    'status' => $dataParkir->status
                ]
            ]);
        }

        return $this->successResponse([
            'status' => $dataParkir->status,

            'message' => 'Parkir belum dibayar.',
            'data' => [
                'kode_parkir' => $dataParkir->kode_parkir,
                'status' => $dataParkir->status
            ]
        ]);
    }
}
