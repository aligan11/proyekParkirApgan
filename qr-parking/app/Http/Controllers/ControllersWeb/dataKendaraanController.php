<?php

namespace App\Http\Controllers\ControllersWeb;

use App\Models\DataKendaraan;
use App\Models\Tipe_kendaraan;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class dataKendaraanController extends Controller
{
    //
    public function tampilDataKendaraan(Request $request)
    {
        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');

        $query = Tipe_kendaraan::query();

        if ($search) {
            $query->where('nama_kendaraan', 'like', '%' . $search . '%');
        }

        $jenisKendaraan = $query->latest()->paginate($perPage)->withQueryString();

        return view('Admin.dataJenisKendaraan', compact('jenisKendaraan', 'perPage', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tarif' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);



        Tipe_kendaraan::create([
            'nama_kendaraan' => $request->nama,
            'tarif_parkir' => $request->tarif,
            'keterangan_tarif' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'nama_kendaraan' => 'required|string|max:255',
            'tarif_parkir' => 'required|numeric',
            'keterangan_tarif' => 'required|string|max:255',
        ]);

        $data = Tipe_kendaraan::findOrFail($id);
        $data->nama_kendaraan = $request->nama_kendaraan;
        $data->tarif_parkir = $request->tarif_parkir;
        $data->keterangan_tarif = $request->keterangan_tarif;
        $data->save();


        return redirect()->route('data.kendaraan')->with('success', 'Data kendaraan berhasil diupdate');
    }

    // Hapus data kendaraan
    public function destroy($id)
    {
        $data = Tipe_kendaraan::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
