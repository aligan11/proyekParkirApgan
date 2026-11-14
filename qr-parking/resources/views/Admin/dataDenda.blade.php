@extends('app')
@section('page-title', 'Data Denda')
@section('page-subtitle', 'Sistem monitoring & pembayaran denda parkir')
@section('content')
    <div class="space-y-6">

        @if (session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false,
                });
            </script>
        @endif

        <!-- PAGE HEADER -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-red-600"></i>
                    Data Denda Kendaraan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Sistem Monitoring &amp; Pembayaran Denda Parkir
                </p>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Denda</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                            Rp {{ number_format($totalDenda, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 text-red-600">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Denda Pending</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">
                            {{ $pending }}
                        </h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD TABLE -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-700">Daftar Denda</h3>
                </div>

                <form method="GET" action="{{ route('parkir.denda') }}" class="relative w-full md:w-72">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="search" name="search" value="{{ request('search') }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3.5 py-2.5 text-sm focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        placeholder="Cari plat / pelanggaran...">
                </form>
            </div>

            <div class="overflow-x-auto">
                <div x-data="{
                    showModal: false,
                    detail: {},
                    openModal(data) {
                        this.detail = data
                        this.showModal = true
                    }
                }">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">No</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Nama</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Plat Nomor</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Jenis Pelanggaran</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-left">Keterangan</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">Waktu</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-right">Denda</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($denda as $item)
                                <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3 text-center text-slate-500">
                                        {{ $loop->iteration + $denda->firstItem() - 1 }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $item->user->nama_lengkap ?? $item->nama }}</td>
                                    <td class="px-4 py-3 font-semibold uppercase text-slate-700">
                                        {{ $item->plat_nomor ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->jenis_pelanggaran }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->keterangan }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">
                                        {{ \Carbon\Carbon::parse($item->waktu_pelanggaran)->format('d-m-Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-red-600">
                                        Rp {{ number_format($item->denda, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($item->status === 'pending')
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700">
                                                Dibayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                @click="openModal({
                                                        nama: '{{ $item->user->nama_lengkap ?? $item->nama }}',
                                                        plat: '{{ $item->plat_nomor }}',
                                                        pelanggaran: '{{ $item->jenis_pelanggaran }}',
                                                        keterangan: '{{ $item->keterangan }}',
                                                        waktu: '{{ \Carbon\Carbon::parse($item->waktu_pelanggaran)->format('d-m-Y H:i') }}',
                                                        denda: 'Rp {{ number_format($item->denda, 0, ',', '.') }}',
                                                        status: '{{ $item->status }}',

                                                        // 👉 AMBIL DARI TABLE DENDA
                                                        gambar: '{{ $item->gambar_kendaraan ? asset('storage/' . $item->gambar_kendaraan) : asset('img/no-car.png') }}'
                                                    })"
                                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 shadow-sm transition">
                                                <i class="fas fa-eye"></i>
                                                <span class="hidden lg:inline">Detail</span>
                                            </button>

                                            <!-- ✔️ SELESAI -->
                                            @if ($item->status === 'pending')
                                                <form action="{{ route('denda.selesai', $item->id) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Tandai denda ini sebagai SELESAI?')">
                                                    @csrf
                                                    @method('PUT')

                                                    <button
                                                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-2 shadow-sm transition">
                                                        <i class="fas fa-check"></i>
                                                        <span class="hidden lg:inline">Selesai</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-100 text-slate-400 text-xs font-semibold px-3 py-2 cursor-not-allowed">
                                                    <i class="fas fa-check"></i>
                                                    <span class="hidden lg:inline">Selesai</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- OVERLAY MODAL -->
                    <div x-show="showModal" x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">

                        <!-- MODAL CARD -->
                        <div @click.outside="showModal = false"
                            class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">

                            <!-- IMAGE HEADER -->
                            <div class="relative">
                                <img :src="detail.gambar ?? '{{ asset('img/no-car.png') }}'" alt="Gambar Kendaraan"
                                    class="w-full h-64 object-cover">

                                <!-- CLOSE -->
                                <button @click="showModal = false"
                                    class="absolute top-3 right-3 bg-black/50 hover:bg-red-600 text-white w-9 h-9 rounded-full flex items-center justify-center transition">
                                    <i class="fas fa-times"></i>
                                </button>

                                <!-- BADGE STATUS -->
                                <span class="absolute bottom-3 left-3 px-4 py-1 text-xs rounded-full font-semibold"
                                    :class="detail.status === 'pending' ?
                                        'bg-amber-400 text-black' :
                                        'bg-emerald-500 text-white'"
                                    x-text="detail.status">
                                </span>
                            </div>

                            <!-- CONTENT -->
                            <div class="p-6 space-y-4 text-sm">

                                <!-- TITLE -->
                                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-car text-red-600"></i>
                                    Detail Denda Kendaraan
                                </h3>

                                <!-- INFO GRID -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Pemilik</p>
                                        <p class="font-semibold text-slate-700" x-text="detail.nama"></p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Plat Nomor</p>
                                        <p class="font-bold uppercase text-slate-700" x-text="detail.plat"></p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jenis Pelanggaran</p>
                                        <p class="text-slate-700" x-text="detail.pelanggaran"></p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Waktu</p>
                                        <p class="text-slate-700" x-text="detail.waktu"></p>
                                    </div>

                                </div>

                                <!-- DENDA -->
                                <div class="flex justify-between items-center bg-red-50 border border-red-100 rounded-xl p-4">
                                    <span class="text-sm text-slate-600">Total Denda</span>
                                    <span class="text-lg font-extrabold text-red-600" x-text="detail.denda"></span>
                                </div>

                                <!-- KETERANGAN -->
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Keterangan</p>
                                    <div class="bg-slate-50 rounded-xl p-4 text-slate-700 leading-relaxed">
                                        <p x-text="detail.keterangan ?? '-'"></p>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="flex justify-end pt-4">
                                    <button @click="showModal = false"
                                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 shadow-sm transition">
                                        Tutup
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-sm text-slate-500">
                    <div>
                        Showing
                        <span class="font-semibold text-slate-700">{{ $denda->firstItem() }}</span>
                        to
                        <span class="font-semibold text-slate-700">{{ $denda->lastItem() }}</span>
                        of
                        <span class="font-semibold text-slate-700">{{ $denda->total() }}</span>
                        entries
                    </div>

                    <div class="flex items-center gap-1">
                        {{-- PREVIOUS --}}
                        @if ($denda->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed">
                                Prev
                            </span>
                        @else
                            <a href="{{ $denda->previousPageUrl() }}"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                                Prev
                            </a>
                        @endif

                        {{-- PAGE NUMBERS --}}
                        @foreach ($denda->getUrlRange(max(1, $denda->currentPage() - 2), min($denda->lastPage(), $denda->currentPage() + 2)) as $page => $url)
                            @if ($page == $denda->currentPage())
                                <span class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-semibold">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- NEXT --}}
                        @if ($denda->hasMorePages())
                            <a href="{{ $denda->nextPageUrl() }}"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                                Next
                            </a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-300 cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
