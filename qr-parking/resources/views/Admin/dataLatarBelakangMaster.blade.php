@extends('app')

@section('page-title', 'Latar Aplikasi')
@section('page-subtitle', 'Pengaturan tampilan latar belakang aplikasi e-Parkir')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-image text-red-600"></i> Latar Belakang Aplikasi
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Pengaturan tema aplikasi parkir kendaraan
                <span class="font-semibold text-slate-700">{{ $logo->nama_aplikasi }}</span>
            </p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="far fa-file-alt text-red-600"></i> Form Rincian / Ubah Data Latar Belakang
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Preview --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pratinjau Gambar</label>
                    <img alt="Gambar Login/Dashboard"
                        class="w-full max-w-xs md:max-w-none rounded-2xl border border-slate-200 object-cover"
                        src="https://storage.googleapis.com/a1aa/image/7ed1747c-0277-424c-56c2-876d9c87c7c0.jpg" />
                </div>

                {{-- Controls --}}
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ganti Gambar Login/Dashboard</label>
                        <input type="file"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Warna Header, Footer &amp; Badge</label>
                        <select
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                            <option>dark</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Warna Tabel &amp; Badge</label>
                        <select
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                            <option>red</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6">
                <button
                    class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
