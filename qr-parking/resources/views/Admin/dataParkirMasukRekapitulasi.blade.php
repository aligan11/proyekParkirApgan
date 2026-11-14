@extends('app')
@section('page-title', 'Data Parkir Masuk')
@section('page-subtitle', 'Monitoring kendaraan yang masuk area parkir')
@section('content')
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-sign-in-alt text-red-600"></i>
                    Data Parkir Masuk
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Monitoring kendaraan yang masuk area parkir secara real-time
                </p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-red-50 text-red-600 px-3.5 py-2 text-sm font-semibold">
                <i class="fas fa-car"></i> Total: {{ $jenisKendaraan->total() }}
            </span>
        </div>

        {{-- MAIN CARD --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">

            {{-- CARD HEADER --}}
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <i class="fas fa-table"></i>
                    </span>
                    <div>
                        <p class="font-bold text-slate-800">Data Parkir Masuk Hari Ini</p>
                        <p class="text-xs text-slate-400">Riwayat kendaraan yang masuk area parkir</p>
                    </div>
                </div>

                <button id="toggleExportForm"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </button>
            </div>

            {{-- EXPORT MODAL --}}
            <div id="exportModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-file-export text-red-600"></i>
                            Export Data Parkir
                        </h3>
                        <button id="closeExportForm" class="text-slate-400 hover:text-slate-700 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('export.parkir') }}" target="_blank">
                        @csrf
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                                    <input type="date" name="from" required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                                    <input type="date" name="to" required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                </div>
                            </div>

                            <div class="rounded-xl bg-amber-50 text-amber-700 px-4 py-3 text-sm flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                Maksimal 500 data per ekspor
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button"
                                    onclick="document.getElementById('exportModal').classList.add('hidden')"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-sm font-semibold px-4 py-2.5 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                    <i class="fas fa-download"></i> Unduh
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 py-4 border-b border-slate-100">
                <form method="GET" class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tampilkan</label>
                            <select name="perPage" onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @foreach ([5, 10, 25, 50, 100] as $limit)
                                    <option value="{{ $limit }}"
                                        {{ request('perPage', 5) == $limit ? 'selected' : '' }}>
                                        {{ $limit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Urutkan</label>
                            <select name="sort" onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>
                                    Terbaru
                                </option>
                                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                                    Terlama
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="w-full md:w-72">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="search" name="search" value="{{ request('search') }}"
                                placeholder="Cari kode parkir / plat..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                        </div>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Plat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Merk</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Waktu Masuk</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide">Lokasi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($jenisKendaraan as $item)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3 text-center font-semibold text-slate-500">
                                    {{ $loop->iteration + $jenisKendaraan->firstItem() - 1 }}
                                </td>
                                <td class="px-4 py-3 font-bold text-red-600">
                                    {{ $item->kode_parkir }}
                                </td>
                                <td class="px-4 py-3 uppercase font-semibold text-slate-800">
                                    {{ $item->platNomor->plat_nomor }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $item->tipeKendaraan->jenis->nama_kendaraan }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $item->merkKendaraan->merk_kendaraan }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-600 px-3 py-1 text-xs font-medium">
                                        {{ $item->waktu_masuk }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-slate-600">
                                    {{ $item->lokasi ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $item->status == 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-400">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 text-sm text-slate-500">
                <span>
                    Showing {{ $jenisKendaraan->firstItem() }}
                    to {{ $jenisKendaraan->lastItem() }}
                    of {{ $jenisKendaraan->total() }} entries
                </span>

                {{ $jenisKendaraan->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>

        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById("exportModal");
        document.getElementById("toggleExportForm").onclick = () => modal.classList.remove("hidden");
        document.getElementById("closeExportForm").onclick = () => modal.classList.add("hidden");
        modal.onclick = e => {
            if (e.target === modal) modal.classList.add("hidden");
        };
    });
</script>
