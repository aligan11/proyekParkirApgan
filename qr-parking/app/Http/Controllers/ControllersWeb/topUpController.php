<?php

namespace App\Http\Controllers\ControllersWeb;

use App\Models\PaketTopup;
use Illuminate\Http\Request;

class topUpController extends Controller
{
    //
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5); // default 5
        $search = $request->input('search');
        $query = PaketTopup::query();

        $query->where('nama', 'like', '%' . $search . '%');

        $paketTopups = $query->latest()->paginate($perPage)->withQueryString();

        return view('Admin.master.tambahPaketTopup', compact('paketTopups'));
    }

    public function store(Request $request)
    {
        // Validasi input

        // dd($request->all());
        $validated = $request->validate([
            'nama' => 'required|string|max:100',            // sesuai nama kolom 'nama'
            'nominal' => 'required|numeric|min:1000',        // nominal bisa decimal, pakai numeric
            'biaya_admin' => 'nullable|numeric|min:0',       // biaya_admin optional
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'nullable|in:on,off,1,0,true,false',

        ]);

        // Simpan data ke database
        $paket = new PaketTopup();
        $paket->nama = $validated['nama'];
        $paket->nominal = $validated['nominal'];
        $paket->biaya_admin = $validated['biaya_admin'] ?? 0;
        $paket->total_bayar = $paket->nominal + $paket->biaya_admin;
        $paket->deskripsi = $validated['deskripsi'] ?? null;
        $paket->is_aktif = $request->has('is_aktif') ? 1 : 0;  // checkbox/toggle aktif atau tidak
        $paket->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('topup-parkir.index')
            ->with('success', 'Paket topup berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $paket = PaketTopup::findOrFail($id); // cari data berdasarkan ID
        $paket->delete(); // hapus data

        return redirect()->route('topup-parkir.index')->with('success', 'Paket topup berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        // dd($request->all());
        $request->validate([
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'biaya_admin' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'is_aktif' => 'nullable|in:on,off,1,0,true,false',
        ]);

        // Ambil data paket berdasarkan ID
        $paket = PaketTopup::findOrFail($id);

        // Update data
        $paket->nama = $request->nama;
        $paket->nominal = $request->nominal;
        $paket->biaya_admin = $request->biaya_admin ?? 0;
        $paket->deskripsi = $request->deskripsi;
        $paket->is_aktif = $request->has('is_aktif') ? 1 : 0;

        $paket->save();

        // Redirect balik dengan pesan sukses
        return redirect()->back()->with('success', 'Paket topup berhasil diperbarui.');
    }
}
