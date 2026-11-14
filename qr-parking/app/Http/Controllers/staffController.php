<?php

namespace App\Http\Controllers;

use App\Http\Requests\listNamaRequestUpdate;
use App\Models\DataParkir;
use App\Models\Denda;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class staffController extends Controller
{
    //
    use ApiResponse;

    public function showByPlat($plat)
    {
        $kendaraan = Kendaraan::with('user')->where('plat_nomor', $plat)->first();

        if (!$kendaraan) {
            return $this->errorResponse("Plat nomor $plat tidak ditemukan.", 404);
        }

        $dataParkir = DataParkir::where('id_plat_nomor', $kendaraan->id)->get();

        if ($dataParkir->isEmpty()) {
            return response()->json([
                'status' => 'denda',
                'message' => 'Kendaraan belum pernah scan parkir.',
            ]);
        }

        $hariIni = now()->toDateString();

        $parkirHariIni = $dataParkir->first(function ($item) use ($hariIni) {
            return \Carbon\Carbon::parse($item->waktu_masuk)->toDateString() === $hariIni;
        });

        if (!$parkirHariIni) {
            return response()->json([
                'status' => 'denda',
                'message' => 'Kendaraan dianggap parkir tanpa scan hari ini.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data parkir hari ini ditemukan.',
            'data' => [
                'plat_nomor' => $kendaraan->plat_nomor,
                'merk_kendaraan' => $kendaraan->merk_kendaraan,
                'tipe_kendaraan' => $kendaraan->jenis->nama_kendaraan ?? '-',

                'warna_kendaraan' => $kendaraan->warna,
                'tarif' => $kendaraan->tarif->tarif_parkir ?? 0,
                'nama_user' => $kendaraan->user->nama_lengkap ?? '-',
                'kode_parkir' => $parkirHariIni->kode_parkir,
                'waktu_masuk' => $parkirHariIni->waktu_masuk,
                'waktu_keluar' => $parkirHariIni->waktu_keluar,
                'lokasi' => $parkirHariIni->lokasi,
                'status' => $parkirHariIni->status,
            ],
        ]);
    }

    public function updateUserParking(listNamaRequestUpdate $request, $id_users)
    {
        $data = $request->validated();

        $user = User::findOrFail($id_users);

        if (isset($data['nama_lengkap'])) {
            $user->nama_lengkap = $data['nama_lengkap'];
        }

        if (isset($data['username'])) {
            $user->username = $data['username'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('foto_users')) {
            $fotoPath = $request->file('foto_users')->store('uploads/foto_user', 'public');
            $user->foto_users = $fotoPath;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diupdate',
            'data' => $user,
        ]);
    }

    public function storeDenda(Request $request)
    {
        $user = Auth::user();

        // Validasi minimal
        $validator = Validator::make($request->all(), [
            'id_kendaraan' => 'required|integer',
            'id_users' => 'required|integer',
            'plat_nomor' => 'required|string|max:20',
            'jenis_pelanggaran' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Simpan ke tabel denda langsung
        $denda = Denda::create([
            'id_users' => $request->id_users,
            'id_kendaraan' => $request->id_kendaraan,
            'plat_nomor' => $request->plat_nomor,
            'jenis_pelanggaran' => $request->jenis_pelanggaran,
            'denda' => 10000,
            'status' => 'pending',
            'keterangan' => 'Denda otomatis karena parkir tanpa proses scan.',
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Denda berhasil dicatat.',
                'data' => $denda,
            ],
            201,
        );
    }

    public function storeDendaNonUser(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'plat_nomor' => 'required|string|max:20',
            'nama_user' => 'nullable|string|max:100',
            'alasan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Simpan denda non-user
        $denda = Denda::create([
            'id_users' => null, // 🔥 non-user
            'plat_nomor' => strtoupper($request->plat_nomor),
            'jenis_pelanggaran' => "Tidak melakukan pemindaian barcode sesuai prosedur parkir atau tidak terdaftar",

            'nama' => $request->nama_user, // simpan nama manual
            'denda' => 15000, // contoh denda non-user
            'status' => 'pending',
            'keterangan' => $request->alasan,
        ]);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Denda non-user berhasil disimpan.',
                'data' => $denda,
            ],
            201,
        );
    }

    public function showKendaraanByPlatNomor(string $plat_nomor)
    {
        // Ambil data kendaraan dengan relasi jenis & tarif berdasarkan plat_nomor
        $kendaraan = Kendaraan::with('jenis', 'tarif')->where('plat_nomor', $plat_nomor)->first();

        if (!$kendaraan) {
            return $this->errorResponse('Kendaraan dengan plat nomor tersebut tidak ditemukan.', 404);
        }

        // Format respons
        $data = [
            'id' => $kendaraan->id,
            'tipe_kendaraan' => optional($kendaraan->jenis)->nama_kendaraan,
            'merk_kendaraan' => $kendaraan->merk_kendaraan,
            'plat_nomor' => $kendaraan->plat_nomor,
            'warna' => $kendaraan->warna,
            'is_aktif' => $kendaraan->is_aktif,
            'tarif' => optional($kendaraan->tarif)->tarif_parkir,
            'id_tarif' => $kendaraan->id_tarif,
            'id_users' => $kendaraan->user_id,
        ];

        return $this->successResponse(
            [
                'data' => $data,
            ],
            'Berhasil mendapatkan data kendaraan berdasarkan plat nomor.',
        );
    }
}
