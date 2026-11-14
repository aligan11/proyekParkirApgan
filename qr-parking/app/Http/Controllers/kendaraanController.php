<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKendaraan;
use App\Models\Kendaraan;
use App\Models\nyubeUser;

use App\Models\Tipe_kendaraan;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = Tipe_kendaraan::select('id', 'nama_kendaraan')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }


    public function store(StoreKendaraan $request)
    {
        $request->validated();
        $user = Auth::user();

        // Cek apakah plat nomor kendaraan ini sudah didaftarkan oleh user
        $existingKendaraan = Kendaraan::where('user_id', $user->id)
            ->where('plat_nomor', $request->plat_nomor)
            ->first();

        if ($existingKendaraan) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Kendaraan dengan plat nomor ini sudah terdaftar.',
            ], 400);
        }

        $kendaraan = new Kendaraan();
        $kendaraan->user_id = $request->user_id;
        $kendaraan->id_jenis = $request->id_jenis;
        $kendaraan->merk_kendaraan = $request->merk_kendaraan;
        $kendaraan->plat_nomor = $request->plat_nomor;
        $kendaraan->warna = $request->warna;
        $kendaraan->id_tarif = $request->id_jenis;
        $kendaraan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kendaraan berhasil ditambahkan.',
            'data' => $kendaraan,
        ], 201);
    }

    public function show(string $user_id)
    {
        // Ambil data kendaraan dengan relasi tipe_kendaraan
        $kendaraans = Kendaraan::with('jenis', 'tarif')
            ->where('user_id', $user_id)
            ->get();

        if ($kendaraans->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }



        // Transformasi data untuk respons
        $data = $kendaraans->map(function ($item) {
            return [
                'id' => $item->id,
                'tipe_kendaraan' => $item->jenis->nama_kendaraan ?? null, // Asumsi nama field
                'merk_kendaraan' => $item->merk_kendaraan,
                'plat_nomor' => $item->plat_nomor,
                'warna' => $item->warna,
                'is_aktif' => $item->is_aktif,
                'id_tarif' => $item->tarif->tarif_parkir,
                'id_tarif2' => $item->id_tarif,
            ];
        });

        return $this->successResponse([
            'data' => $data,
        ], 'Berhasil mendapatkan data.');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::find($id);

        if (!$kendaraan) {
            return $this->errorResponse('Kendaraan tidak ditemukan.', 404);
        }

        $kendaraan->delete();

        return $this->successResponse(null, 'Kendaraan berhasil dihapus.');
    }

    public function updateIsAktif(Request $request, $id)
    {
        $kendaraan = Kendaraan::find($id);

        if (!$kendaraan) {
            return $this->errorResponse('Kendaraan tidak ditemukan.', 404);
        }

        // Nonaktifkan semua kendaraan milik user
        Kendaraan::where('user_id', $kendaraan->user_id)->update(['is_aktif' => 'off']);

        // Aktifkan kendaraan ini
        $kendaraan->is_aktif = 'on';
        $kendaraan->save();

        return $this->successResponse(null, 'Kendaraan berhasil diaktifkan.');
    }
}
