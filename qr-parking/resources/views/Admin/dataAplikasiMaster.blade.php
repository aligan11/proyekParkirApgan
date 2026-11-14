@extends('app')

@section('page-title', 'Data Aplikasi')
@section('page-subtitle', 'Pengaturan identitas & informasi aplikasi e-Parkir')

@section('content')
    <div class="space-y-6">

        {{-- Page header --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img alt="Logo Parkir" class="w-16 h-16 rounded-2xl object-cover ring-1 ring-slate-200"
                    src="{{ asset('img/' . ($property->logo_aplikasi ?? 'parking.jpg')) }}" />
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-cogs text-red-600"></i> Data Aplikasi
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">Form rincian / ubah data aplikasi parkir</p>
                </div>
            </div>
        </div>

        {{-- Alert success --}}
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Form card --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-list-alt text-red-600"></i> Rincian Data Aplikasi
                </h2>
            </div>

            <form id="resetForm" action="{{ route('aplikasi.update') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Aplikasi</label>
                        <input type="text" name="nama_aplikasi" value="{{ old('nama_aplikasi', $property->nama_aplikasi) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Parkir</label>
                        <input type="text" name="kode_parkir" value="{{ old('kode_parkir', $property->kode_parkir) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ganti Logo Aplikasi</label>
                        <input type="file" name="logo"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-600 file:px-3 file:py-1.5 file:text-xs file:font-semibold">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Perusahaan / Pengelola Parkir</label>
                        <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $property->owner_parkir) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Perusahaan</label>
                        <input type="text" name="alamat" value="{{ old('alamat', $property->alamat_perusahaan) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $property->email_perusahaan) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor HP</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $property->no_perusahaan) }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                    <button type="button" onclick="confirmReset()"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        <i class="fas fa-undo"></i>
                        <span>Reset</span>
                    </button>

                    <a href="{{ url()->previous() }}"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                        <i class="fas fa-save"></i>
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        function confirmReset() {
            Swal.fire({
                title: 'Yakin reset ke default?',
                text: "Data akan kembali ke pengaturan awal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resetForm').submit();
                }
            });
        }
    </script>
@endsection
