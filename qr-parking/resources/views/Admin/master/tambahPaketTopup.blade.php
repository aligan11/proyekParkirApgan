@extends('app')

@section('page-title', 'Paket Topup')
@section('page-subtitle', 'Manajemen paket topup saldo e-Parkir')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-coins text-red-600"></i> Paket Topup
            </h1>
            <p class="text-sm text-slate-500 mt-1">Kelola paket topup saldo parkir</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Form Tambah Paket Topup (Kiri) --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-red-600"></i> Tambah Paket Topup
                </h2>
            </div>

            <form action="{{ route('topup-parkir.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="nama" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Paket</label>
                    <input type="text" id="nama" name="nama" placeholder="Contoh: Paket Silver, Paket Gold"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        required>
                </div>

                <div>
                    <label for="nominal" class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Topup (Rp)</label>
                    <input type="number" step="0.01" id="nominal" name="nominal"
                        placeholder="Contoh: 50000, 100000"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        required>
                </div>

                <div>
                    <label for="biaya_admin" class="block text-sm font-semibold text-slate-700 mb-1.5">Biaya Admin (Rp)</label>
                    <input type="number" step="0.01" id="biaya_admin" name="biaya_admin"
                        placeholder="Contoh: 1000, 2000" value="0"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi (opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        placeholder="Keterangan tambahan..."></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_aktif" name="is_aktif" checked
                        class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-400">
                    <label for="is_aktif" class="text-sm font-semibold text-slate-700">Aktif</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                        <i class="fas fa-save"></i>
                        <span>Simpan</span>
                    </button>
                    <button type="reset"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Data Paket Topup (Kanan) --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-table text-red-600"></i> Data Paket Topup
                </h2>
            </div>

            <div class="p-6 pt-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Nama Paket</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Nominal (Rp)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paketTopups as $index => $paket)
                                <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                    <td class="px-4 py-3">{{ $paketTopups->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $paket->nama }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($paket->nominal, 2, ',', '.') }}</td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($paket->is_aktif)
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700">Aktif</span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <a href="#" data-modal-target="modalEdit{{ $paket->id }}"
                                                data-modal-toggle="modalEdit{{ $paket->id }}"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-amber-50 text-amber-700 hover:bg-amber-100"
                                                title="Edit Paket">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <!-- Modal Edit -->
                                            <div id="modalEdit{{ $paket->id }}" tabindex="-1"
                                                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                                                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                                                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                                                        <h3 class="text-sm font-bold text-slate-900">Edit Paket Topup</h3>
                                                        <button type="button"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100"
                                                            data-modal-hide="modalEdit{{ $paket->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('topup-parkir.update', $paket->id) }}"
                                                        method="POST" class="p-6 space-y-4">
                                                        @csrf
                                                        @method('PUT')

                                                        <div>
                                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Paket</label>
                                                            <input type="text" name="nama" value="{{ $paket->nama }}"
                                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                                                required>
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Topup (Rp)</label>
                                                            <input type="number" name="nominal" step="0.01"
                                                                value="{{ $paket->nominal }}"
                                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                                                required>
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Biaya Admin (Rp)</label>
                                                            <input type="number" name="biaya_admin" step="0.01"
                                                                value="{{ $paket->biaya_admin }}"
                                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                                        </div>

                                                        <div>
                                                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                                                            <textarea name="deskripsi" rows="3"
                                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">{{ $paket->deskripsi }}</textarea>
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <input type="checkbox" name="is_aktif"
                                                                id="editAktif{{ $paket->id }}"
                                                                class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-400"
                                                                {{ $paket->is_aktif ? 'checked' : '' }}>
                                                            <label for="editAktif{{ $paket->id }}"
                                                                class="text-sm font-semibold text-slate-700">Aktif</label>
                                                        </div>

                                                        <div class="flex justify-end gap-3 pt-4">
                                                            <button type="button"
                                                                class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition"
                                                                data-modal-hide="modalEdit{{ $paket->id }}">Batal</button>
                                                            <button type="submit"
                                                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                                                <i class="fas fa-save"></i> Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Tombol Hapus --}}
                                            <form id="delete-form-{{ $paket->id }}"
                                                action="{{ route('topup-parkir.destroy', $paket->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-red-50 text-red-600 hover:bg-red-100"
                                                    onclick="confirmDelete({{ $paket->id }})" title="Hapus Paket">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                        Data paket topup belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $paketTopups->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>


@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
@endif

<script>
    document.querySelectorAll('[data-modal-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-modal-target');
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-hide]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-modal-hide');
            document.getElementById(targetId).classList.add('hidden');
        });
    });
</script>
@endsection
