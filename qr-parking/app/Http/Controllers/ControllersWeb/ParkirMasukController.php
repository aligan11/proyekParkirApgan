<?php

namespace App\Http\Controllers\ControllersWeb;

use Illuminate\Http\Request;
use App\Models\Parkir;

class ParkirMasukController extends Controller
{
    public function create()
    {
        return view('dashboard.dataMasuk'); // ganti dengan nama blade view kamu
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_parkir' => 'required|unique:parkirs,kode_parkir',
            'jenis_kendaraan' => 'required',
            'plat_nomor' => 'required',
            'tipe_kendaraan' => 'required',
            'warna_kendaraan' => 'required',
        ]);

        Parkir::create([
            'kode_parkir' => $request->kode_parkir,
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'plat_nomor' => $request->plat_nomor,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'warna_kendaraan' => $request->warna_kendaraan,
            'waktu_masuk' => now(),
            'status' => 'masuk'
        ]);

        return redirect()->back()->with('success', 'Data parkir berhasil ditambahkan');
    }
}
