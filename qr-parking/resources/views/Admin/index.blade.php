@extends('app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional parkir secara real-time')

@section('content')
    <div class="space-y-6">

        {{-- ================= WELCOME ================= --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 flex flex-col md:flex-row md:items-center gap-6">
                <div class="flex-1">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold">
                        <i class="fas fa-circle text-[6px]"></i> Sistem Aktif
                    </span>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Selamat datang,
                        <span class="text-red-600">{{ session('adminData')->nama_lengkap ?? 'Administrator' }}</span>
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed max-w-2xl">
                        Kelola pendataan kendaraan masuk &amp; keluar, denda, serta pendapatan parkir dari satu panel.
                        Disarankan menggunakan <b class="text-slate-700">Google Chrome</b> agar seluruh fitur berjalan
                        optimal.
                    </p>
                </div>
                <img src="{{ asset('img/banner.png') }}" alt="Banner"
                    class="w-40 sm:w-52 mx-auto md:mx-0 shrink-0">
            </div>
        </div>

        {{-- ================= STATISTIK ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">

            @php
                $stats = [
                    [
                        'label' => 'Total Data Parkir',
                        'value' => number_format($totalParkir, 0, ',', '.'),
                        'sub' => $parkirHariIni . ' kendaraan hari ini',
                        'icon' => 'fa-car',
                        'tone' => 'bg-blue-50 text-blue-600',
                    ],
                    [
                        'label' => 'Jenis Kendaraan',
                        'value' => number_format($jenisKendaraan, 0, ',', '.'),
                        'sub' => 'Master tarif aktif',
                        'icon' => 'fa-layer-group',
                        'tone' => 'bg-violet-50 text-violet-600',
                    ],
                    [
                        'label' => 'Denda Pending',
                        'value' => number_format($dendaPending, 0, ',', '.'),
                        'sub' => 'Rp ' . number_format($totalDenda, 0, ',', '.') . ' belum dibayar',
                        'icon' => 'fa-money-bill-wave',
                        'tone' => 'bg-amber-50 text-amber-600',
                    ],
                    [
                        'label' => 'Total Income',
                        'value' => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
                        'sub' => 'Rp ' . number_format($incomeHariIni, 0, ',', '.') . ' hari ini',
                        'icon' => 'fa-wallet',
                        'tone' => 'bg-emerald-50 text-emerald-600',
                    ],
                ];
            @endphp

            @foreach ($stats as $s)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5
                            hover:shadow-md hover:-translate-y-0.5 transition duration-200">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $s['label'] }}</p>
                            <p class="mt-2 text-2xl font-extrabold text-slate-900 truncate">{{ $s['value'] }}</p>
                        </div>
                        <span class="w-11 h-11 flex items-center justify-center rounded-xl {{ $s['tone'] }} shrink-0">
                            <i class="fas {{ $s['icon'] }}"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $s['sub'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ================= MENU CEPAT ================= --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fas fa-bolt text-red-500"></i>
                <h2 class="font-bold text-slate-800">Menu Cepat</h2>
            </div>

            <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $menus = [
                        ['route' => 'data.parkir.masuk', 'icon' => 'fa-sign-in-alt', 'text' => 'Parkir Masuk'],
                        ['route' => 'data.parkir.keluar', 'icon' => 'fa-sign-out-alt', 'text' => 'Parkir Keluar'],
                        ['route' => 'data.parkir.gabungan', 'icon' => 'fa-layer-group', 'text' => 'Data Gabungan'],
                        ['route' => 'parkir.income', 'icon' => 'fa-chart-line', 'text' => 'Income Parkir'],
                        ['route' => 'parkir.denda', 'icon' => 'fa-money-bill-wave', 'text' => 'Data Denda'],
                        ['route' => 'data.kendaraan', 'icon' => 'fa-car', 'text' => 'Jenis & Tarif'],
                        ['route' => 'topup-parkir.index', 'icon' => 'fa-coins', 'text' => 'Paket Topup'],
                        ['route' => 'data.aplikasi', 'icon' => 'fa-cogs', 'text' => 'Pengaturan'],
                    ];
                @endphp

                @foreach ($menus as $m)
                    <a href="{{ route($m['route']) }}"
                        class="group flex flex-col items-center gap-3 rounded-xl border border-slate-200 p-4 text-center
                               hover:border-red-300 hover:bg-red-50/40 transition">
                        <span
                            class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-600
                                   group-hover:bg-red-600 group-hover:text-white transition">
                            <i class="fas {{ $m['icon'] }}"></i>
                        </span>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-red-700">{{ $m['text'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
@endsection
