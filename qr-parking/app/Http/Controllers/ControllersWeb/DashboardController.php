<?php

namespace App\Http\Controllers\ControllersWeb;

use App\Models\DataParkir;
use App\Models\Denda;
use App\Models\Parkir;
use App\Models\PropertyAplikasi;
use App\Models\Tipe_kendaraan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function app()
    {
        $logo = PropertyAplikasi::first();
        return view('app.blade.php', compact('logo'));
    }
    //
    public function index()
    {
        $hariIni = now()->toDateString();

        $totalParkir    = DataParkir::count();
        $parkirHariIni  = DataParkir::whereDate('waktu_masuk', $hariIni)->count();
        $jenisKendaraan = Tipe_kendaraan::count();

        $dendaPending = Denda::where('status', 'pending')->count();
        $totalDenda   = Denda::where('status', 'pending')->sum('denda');

        $totalIncome   = DataParkir::where('status', 'Sudah Dibayar')->sum('fee_admin');
        $incomeHariIni = DataParkir::where('status', 'Sudah Dibayar')
            ->whereDate('waktu_keluar', $hariIni)
            ->sum('fee_admin');

        return view('Admin.index', compact(
            'totalParkir',
            'parkirHariIni',
            'jenisKendaraan',
            'dendaPending',
            'totalDenda',
            'totalIncome',
            'incomeHariIni'
        ));
    }

    public function indexDenda(Request $request)
    {
        $perPage = $request->get('show', 10);
        $search  = $request->get('search');

        $denda = Denda::where('status', 'pending') // 🔥 FILTER UTAMA
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('plat_nomor', 'like', "%{$search}%")
                        ->orWhere('jenis_pelanggaran', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderBy('waktu_pelanggaran', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        // Total semua denda (opsional: semua / hanya pending)
        $totalDenda = Denda::where('status', 'pending')->sum('denda');

        // Jumlah pending
        $pending = Denda::where('status', 'pending')->count();

        return view('admin.dataDenda', compact(
            'denda',
            'totalDenda',
            'pending'
        ));
    }

    public function dendaSelesai($id)
    {
        $denda = Denda::findOrFail($id);
        $denda->status = 'dibayar';
        $denda->save();

        return back()->with('success', 'Denda berhasil diselesaikan');
    }



    public function incomeAdmin(Request $request)
    {
        $search  = $request->get('search');
        $perPage = 10;

        // =====================
        // QUERY BASE (HANYA YANG SUDAH SELESAI / PAID)
        // =====================
        $baseQuery = DataParkir::where('status', 'Sudah Dibayar');

        // =====================
        // SUMMARY
        // =====================
        $totalTransaksi = (clone $baseQuery)->count();

        $feeAdmin = (clone $baseQuery)->value('fee_admin') ?? 250;

        $totalIncome = (clone $baseQuery)->sum('fee_admin');

        // =====================
        // TABLE DATA + SEARCH
        // =====================
        $transaksi = $baseQuery
            ->when($search, function ($q) use ($search) {
                $q->where('kode_parkir', 'like', "%{$search}%")
                    ->orWhereHas('platNomor', function ($p) use ($search) {
                        $p->where('plat_nomor', 'like', "%{$search}%");
                    });
            })
            ->with(['platNomor'])
            ->orderBy('waktu_keluar', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('admin.dataIncome', compact(
            'transaksi',
            'totalTransaksi',
            'feeAdmin',
            'totalIncome'
        ));
    }
    public function tampilDataParkir(Request $request)
    {
        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');
        $sort = $request->input('sort', 'desc'); // default 'desc' (terbaru)

        $query = DataParkir::query();

        if ($search) {
            $query
                ->whereHas('platNomor', function ($q) use ($search) {
                    $q->where('plat_nomor', 'like', '%' . $search . '%');
                })
                ->with('jenis', 'tarif');
        }

        $jenisKendaraan = $query->orderBy('waktu_masuk', $sort)->paginate($perPage)->withQueryString();

        return view('Admin.dataParkirMasukRekapitulasi', compact('perPage', 'search', 'jenisKendaraan', 'sort'));
    }

    public function tampilDataParkirKeluar(Request $request)
    {
        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');
        $sort = $request->input('sort', 'desc'); // default 'desc' (terbaru)

        $query = DataParkir::query();

        if ($search) {
            $query
                ->whereHas('platNomor', function ($q) use ($search) {
                    $q->where('plat_nomor', 'like', '%' . $search . '%');
                })
                ->with('jenis', 'tarif');
        }

        $jenisKendaraan = $query->orderBy('waktu_masuk', $sort)->paginate($perPage)->withQueryString();

        return view('Admin.dataParkirKeluarRekapitulasi', compact('perPage', 'search', 'jenisKendaraan', 'sort'));
    }

    public function tampilDataParkirGabungan(Request $request)
    {
        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');
        $sort = $request->input('sort', 'desc'); // default 'desc' (terbaru)

        $query = DataParkir::query();

        if ($search) {
            $query
                ->whereHas('platNomor', function ($q) use ($search) {
                    $q->where('plat_nomor', 'like', '%' . $search . '%');
                })
                ->with('jenis', 'tarif');
        }

        $jenisKendaraan = $query->orderBy('waktu_masuk', $sort)->paginate($perPage)->withQueryString();

        return view('Admin.dataParkirGabunganRekapitulasi', compact('perPage', 'search', 'jenisKendaraan', 'sort'));
    }
    public function tampilDataAplikasi()
    {
        $property = PropertyAplikasi::first();

        return view('Admin.dataAplikasiMaster', compact('property'));
    }

    public function tampilDataKendaraan()
    {
        return view('Admin.dataJenisKendaraan');
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'kode_parkir' => 'required|string|max:50',
            'nama_perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $property = PropertyAplikasi::first();

        if (!$property) {
            $property = new PropertyAplikasi();
        }

        $property->nama_aplikasi = $request->nama_aplikasi;
        $property->kode_parkir = $request->kode_parkir;
        $property->owner_parkir = $request->nama_perusahaan;
        $property->alamat_perusahaan = $request->alamat;
        $property->email_perusahaan = $request->email;
        $property->no_perusahaan = $request->telepon;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            $property->logo_aplikasi = $filename;
        }

        $property->save();

        return redirect()->route('data.aplikasi')->with('success', 'Data aplikasi berhasil diperbarui.');
    }

    public function reset()
    {
        $property = PropertyAplikasi::first();

        // Set data default
        $property->nama_aplikasi = 'GANPARKING';
        $property->kode_parkir = 'PARKIR';
        $property->owner_parkir = 'Gan Company';
        $property->alamat_perusahaan = 'Jl. Default No. 1, Jakarta';
        $property->email_perusahaan = 'info@ganparking.com';
        $property->no_perusahaan = '08123456789';

        // Hapus logo jika ada
        if ($property->logo_aplikasi) {
            $filePath = public_path('img/' . $property->logo_aplikasi);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $property->logo_aplikasi = null;
        }

        $property->save();

        return redirect()->back()->with('success', 'Data aplikasi telah dikembalikan ke pengaturan default.');
    }

    public function tampilDataBg()
    {
        return view('Admin.dataLatarBelakangMaster');
    }

    public function tampilDataUsers()
    {
        return view('Admin.dataUsersMaster');
    }
}
