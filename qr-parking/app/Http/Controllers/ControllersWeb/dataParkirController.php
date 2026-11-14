<?php

namespace App\Http\Controllers\ControllersWeb;

use App\Models\DataKendaraan;
use App\Models\DataParkir;
use App\Models\Parkir;
use App\Models\PropertyAplikasi;
use App\Models\Tipe_kendaraan;
use Illuminate\Http\Request;

class dataParkirController extends Controller
{
    //

    public function index(Request $request)
    {
        $kode = $this->generateKodeParkir();
        $jenis = Tipe_kendaraan::all();

        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');

        // Mulai query + load relasi
        $query = DataParkir::with([
            'user',
            'merkKendaraan',
            'platNomor',
            'tipeKendaraan',
            'warnaKendaraan',
            'tarif',
        ]);

        // Jika ada pencarian
        if ($search) {
            $query->where('plat_nomor', 'like', '%' . $search . '%');
        }

        // Pagination + query string
        $jenisKendaraan = $query->latest()->paginate($perPage)->withQueryString();

        return view('Admin.dataDenda', compact('jenis', 'perPage', 'search', 'jenisKendaraan', 'kode'));
    }


    // Pisahkan fungsi generateKodeParkir agar rapi
    private function generateKodeParkir()
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

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'kode_parkir' => 'required|string',
            'jenis_kendaraan' => 'required|string',
            'plat_nomor' => 'required|string',
            'tipe_merek' => 'required|string',
            'warna' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $plat = strtoupper($request->plat_nomor);
        $lat = $request->latitude;
        $lon = $request->longitude;

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&zoom=18&addressdetails=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Ganti dengan emailmu agar tidak diblokir
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyParkingApp/1.0 (youremail@example.com)');

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);

        // Coba ambil dari city, kalau tidak ada pakai town, kalau tidak ada pakai village
        $address = $data->address ?? null;

        $locationName = $address->city
            ?? $address->town
            ?? $address->village
            ?? $address->county
            ?? 'Lokasi tidak diketahui';



        DataParkir::create([
            'kode_parkir'     => $request->kode_parkir,
            'id_jenis_kendaraan' => $request->jenis_kendaraan,
            'plat_nomor'      => $plat,
            'tipe_kendaraan'      => $request->tipe_merek,
            'warna_kendaraan'           => $request->warna,
            'status'            => 'Sedang Proses',
            'waktu_masuk'      => now(),
            'lokasi'           => $locationName ?? '-',
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }
}
