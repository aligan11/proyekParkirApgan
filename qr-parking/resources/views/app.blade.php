<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ $nama_aplikasi ?? 'GANPARKING' }}</title>

    <!-- Tailwind (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="h-full bg-slate-100 text-slate-700 antialiased" x-data="{ sidebarOpen: false, profileOpen: false }">

    {{-- Overlay (mobile) --}}
    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    {{-- ================= SIDEBAR ================= --}}
    <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 w-72 transform bg-white border-r border-slate-200
               transition-transform duration-200 ease-in-out lg:translate-x-0 flex flex-col">

        {{-- Brand --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100 shrink-0">
            @if (!empty($logo_aplikasi))
                <img src="{{ asset('img/' . $logo_aplikasi) }}" alt="Logo"
                    class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200">
            @else
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-600 text-white shadow-sm">
                    <i class="fas fa-parking"></i>
                </span>
            @endif
            <div class="leading-tight">
                <p class="font-extrabold text-slate-900 tracking-tight">
                    {{ $nama_aplikasi ?? 'GANPARKING' }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium">Panel Administrator</p>
            </div>
            <button @click="sidebarOpen = false" class="ml-auto text-slate-400 hover:text-slate-700 lg:hidden">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">

            @php
                $linkBase = 'flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition';
                $linkOn = 'bg-red-50 text-red-600';
                $linkOff = 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
                $section = 'px-3 mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400';
            @endphp

            {{-- Utama --}}
            <div>
                <p class="{{ $section }}">Utama</p>
                <a href="{{ route('dashboard') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $linkOn : $linkOff }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard
                </a>
            </div>

            {{-- Transaksi Parkir --}}
            <div>
                <p class="{{ $section }}">Parkir</p>
                <div class="space-y-1">
                    <a href="{{ route('data.parkir.masuk') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.parkir.masuk') ? $linkOn : $linkOff }}">
                        <i class="fas fa-sign-in-alt w-5 text-center"></i> Parkir Masuk
                    </a>
                    <a href="{{ route('data.parkir.keluar') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.parkir.keluar') ? $linkOn : $linkOff }}">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Parkir Keluar
                    </a>
                    <a href="{{ route('data.parkir.gabungan') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.parkir.gabungan') ? $linkOn : $linkOff }}">
                        <i class="fas fa-layer-group w-5 text-center"></i> Data Gabungan
                    </a>
                    <a href="{{ route('parkir.income') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('parkir.income') ? $linkOn : $linkOff }}">
                        <i class="fas fa-chart-line w-5 text-center"></i> Income
                    </a>
                    <a href="{{ route('parkir.denda') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('parkir.denda') ? $linkOn : $linkOff }}">
                        <i class="fas fa-money-bill-wave w-5 text-center"></i> Denda
                    </a>
                </div>
            </div>

            {{-- Master Data --}}
            <div>
                <p class="{{ $section }}">Master Data</p>
                <div class="space-y-1">
                    <a href="{{ route('data.aplikasi') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.aplikasi') ? $linkOn : $linkOff }}">
                        <i class="fas fa-cogs w-5 text-center"></i> Data Aplikasi
                    </a>
                    <a href="{{ route('data.kendaraan') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.kendaraan') ? $linkOn : $linkOff }}">
                        <i class="fas fa-car w-5 text-center"></i> Jenis Kendaraan
                    </a>
                    <a href="{{ route('topup-parkir.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('topup-parkir.*') ? $linkOn : $linkOff }}">
                        <i class="fas fa-coins w-5 text-center"></i> Paket Topup
                    </a>
                    <a href="{{ route('data.bg') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('data.bg') ? $linkOn : $linkOff }}">
                        <i class="fas fa-image w-5 text-center"></i> Latar Aplikasi
                    </a>
                </div>
            </div>

            {{-- Pengguna (hanya admin_super) --}}
            @if (session('role') === 'admin_super')
                <div>
                    <p class="{{ $section }}">Pengguna</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('admin.index') ? $linkOn : $linkOff }}">
                            <i class="fas fa-user-shield w-5 text-center"></i> Data Admin
                        </a>
                        <a href="{{ route('admin.createUser') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('admin.createUser') ? $linkOn : $linkOff }}">
                            <i class="fas fa-user-plus w-5 text-center"></i> Data User
                        </a>
                        <a href="{{ route('admin.createStaff') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('admin.createStaff') ? $linkOn : $linkOff }}">
                            <i class="fas fa-user-tie w-5 text-center"></i> Data Staff
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        {{-- Footer: current user --}}
        <div class="shrink-0 border-t border-slate-100 p-3">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                @php $fotoAdmin = session('adminData')->foto_admin ?? null; @endphp
                @if ($fotoAdmin)
                    <img src="{{ asset($fotoAdmin) }}" alt="Admin"
                        class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200">
                @else
                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-200 text-slate-500">
                        <i class="fas fa-user"></i>
                    </span>
                @endif
                <div class="min-w-0 leading-tight">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ session('adminData')->nama_lengkap ?? 'Administrator' }}
                    </p>
                    <p class="text-[11px] text-slate-400 truncate">
                        {{ session('adminData')->username ?? 'admin' }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form ml-auto">
                    @csrf
                    <button type="submit" title="Logout"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                               hover:bg-red-50 hover:text-red-600 transition">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ================= MAIN ================= --}}
    <div class="lg:pl-72 min-h-full flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 h-16 bg-white/80 backdrop-blur border-b border-slate-200">
            <div class="h-full px-4 sm:px-6 flex items-center gap-4">
                <button @click="sidebarOpen = true"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500
                           hover:bg-slate-100 lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="min-w-0">
                    <p class="text-base sm:text-lg font-bold text-slate-900 truncate">
                        @yield('page-title', 'Dashboard')
                    </p>
                    <p class="text-xs text-slate-400 truncate hidden sm:block">
                        @yield('page-subtitle', 'Sistem Manajemen E-Parkir Kendaraan')
                    </p>
                </div>

                {{-- Profile dropdown --}}
                <div class="relative ml-auto">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                        class="flex items-center gap-3 pl-2 pr-3 py-1.5 rounded-xl hover:bg-slate-100 transition">
                        <span class="hidden sm:block text-right leading-tight">
                            <span class="block text-sm font-semibold text-slate-800">
                                {{ session('adminData')->nama_lengkap ?? 'Administrator' }}
                            </span>
                            <span class="block text-[11px] text-slate-400">Online</span>
                        </span>
                        @if ($fotoAdmin)
                            <img src="{{ asset($fotoAdmin) }}" alt="Admin"
                                class="w-9 h-9 rounded-full object-cover ring-1 ring-slate-200">
                        @else
                            <span
                                class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-200 text-slate-500">
                                <i class="fas fa-user"></i>
                            </span>
                        @endif
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden sm:block"></i>
                    </button>

                    <div x-cloak x-show="profileOpen" x-transition.origin.top.right
                        class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-slate-100
                               bg-white shadow-lg">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                {{ session('adminData')->nama_lengkap ?? 'Administrator' }}
                            </p>
                            <p class="text-xs text-slate-400 truncate">
                                {{ session('adminData')->username ?? 'admin' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-600
                                       hover:bg-red-50 hover:text-red-600 transition">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <footer class="px-6 py-4 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ $nama_aplikasi ?? 'GANPARKING' }}
            &middot; {{ $owner_parkir ?? 'Gan Company' }}
        </footer>
    </div>

    {{-- Modal-level stack: dirender di level body agar overlay menutupi seluruh layar --}}
    @stack('modals')

    @stack('scripts')

    {{-- Konfirmasi logout global (sidebar & topbar) memakai SweetAlert2 --}}
    <script>
        document.addEventListener('submit', function(event) {
            const form = event.target;
            if (!form.classList.contains('logout-form')) return;

            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar dari sesi ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
</body>

</html>
