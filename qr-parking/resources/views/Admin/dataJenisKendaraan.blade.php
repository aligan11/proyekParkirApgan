@extends('app')

@section('page-title', 'Jenis Kendaraan')
@section('page-subtitle', 'Manajemen master jenis kendaraan e-Parkir')

@section('content')
<div class="space-y-6">

    {{-- Alert Success --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#ef4444'
            });
        </script>
    @endif

    {{-- Page header --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-car text-red-600"></i> Data Jenis Kendaraan
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Manajemen master jenis kendaraan
                <span class="font-semibold text-slate-700">{{ $logo->nama_aplikasi }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- FORM TAMBAH --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-red-600"></i> Tambah Jenis Kendaraan
                </h2>
            </div>

            <form action="{{ route('data.kendaraan.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Kendaraan</label>
                    <input type="text" name="nama"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        placeholder="Contoh: Mobil">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarif Parkir</label>
                    <input type="number" name="tarif"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        placeholder="Contoh: 3000">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan Tarif</label>
                    <select name="keterangan"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                        <option value="Flat">Flat</option>
                        <option value="Per Jam">Per Jam</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="reset"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>

        {{-- TABEL DATA --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-table text-red-600"></i> Data Jenis Kendaraan
                </h2>
            </div>

            <div class="p-6 pt-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Tarif</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Keterangan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($jenisKendaraan as $item)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ $item->nama_kendaraan }}</td>
                                <td class="px-4 py-3">Rp{{ number_format($item->tarif_parkir,0,',','.') }}</td>
                                <td class="px-4 py-3">{{ $item->keterangan_tarif }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="btn-edit inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-amber-50 text-amber-700 hover:bg-amber-100"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kendaraan }}"
                                            data-tarif="{{ $item->tarif_parkir }}"
                                            data-keterangan="{{ $item->keterangan_tarif }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button
                                            class="btn-hapus inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-red-50 text-red-600 hover:bg-red-100"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama_kendaraan }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between mt-4 text-sm">
                    {{ $jenisKendaraan->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div id="modalEdit"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-edit text-red-600"></i> Edit Jenis Kendaraan
            </h3>
            <button onclick="closeEdit()" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formEdit" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Kendaraan</label>
                <input type="text" name="nama_kendaraan" id="editNama"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarif Parkir</label>
                <input type="number" name="tarif_parkir" id="editTarif"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                <select name="keterangan_tarif" id="editKeterangan"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                    <option value="Flat">Flat</option>
                    <option value="Per Jam">Per Jam</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeEdit()"
                    class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
    const modalEdit = document.getElementById('modalEdit');
    const formEdit = document.getElementById('formEdit');

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('editNama').value = btn.dataset.nama;
            document.getElementById('editTarif').value = btn.dataset.tarif;
            document.getElementById('editKeterangan').value = btn.dataset.keterangan;

            formEdit.action = `/update-jenis-kendaraan/${btn.dataset.id}`;
            modalEdit.classList.remove('hidden');
        }
    });

    function closeEdit() {
        modalEdit.classList.add('hidden');
    }

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.onclick = () => {
            Swal.fire({
                title: 'Hapus Data?',
                text: `Yakin hapus ${btn.dataset.nama}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Hapus'
            }).then(res => {
                if (res.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/data-kendaraan/delete/${btn.dataset.id}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });
</script>
@endsection
