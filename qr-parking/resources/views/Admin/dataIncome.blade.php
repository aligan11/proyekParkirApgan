@extends('app')
@section('page-title', 'Income Admin Parkir')
@section('page-subtitle', 'Pendapatan admin dari fee Rp 250 per transaksi parkir')
@section('content')
    <div class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-red-600"></i>
                    Income Admin Parkir
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Pendapatan admin dari transaksi parkir (Rp 250 / transaksi)
                </p>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- TOTAL TRANSAKSI -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Transaksi</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                            {{ $totalTransaksi }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 text-red-600">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- FEE ADMIN -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Fee Admin</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                            Rp {{ number_format($feeAdmin, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>

            <!-- TOTAL INCOME -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Income</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">

            <!-- TABLE HEADER -->
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="fas fa-table text-red-600"></i> Detail Income Transaksi
                </h3>

                <form method="GET" class="relative w-full md:w-72">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="search" name="search" value="{{ request('search') }}"
                        placeholder="Cari plat / kode parkir..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3.5 py-2.5 text-sm focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                </form>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">No</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Kode Parkir</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Plat Nomor</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">Waktu Bayar</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-right">Fee Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $i => $item)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3 text-center text-slate-500">
                                    {{ $transaksi->firstItem() + $i }}
                                </td>

                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $item->kode_parkir }}
                                </td>

                                <td class="px-4 py-3 uppercase font-semibold text-slate-700">
                                    {{ $item->platNomor->plat_nomor ?? '-' }}
                                </td>

                                <td class="px-4 py-3 text-center text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->waktu_keluar)->format('d-m-Y H:i') }}
                                </td>

                                <td class="px-4 py-3 text-right font-bold text-emerald-600">
                                    Rp {{ number_format($item->fee_admin, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                    <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                    Tidak ada data transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- FOOTER -->
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 text-sm text-slate-500">
                <span>Total transaksi: <b class="text-slate-700">{{ $totalTransaksi }}</b></span>

                <span class="font-semibold">
                    Total Income:
                    <span class="text-emerald-600">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                </span>

            </div>

        </div>

    </div>
@endsection
