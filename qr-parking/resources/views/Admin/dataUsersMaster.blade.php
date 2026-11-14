@extends('app')

@section('page-title', 'Manajemen Admin')
@section('page-subtitle', 'Kelola akun admin aplikasi e-Parkir')

@section('content')
    <div class="space-y-6">

        {{-- ALERT SUCCESS --}}
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

        {{-- PAGE HEADER --}}
        <div
            class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-shield text-red-600"></i>
                    Manajemen Admin e-Parkir
                </h1>
                <p class="text-sm text-slate-500 mt-1">Kelola admin aplikasi parkir</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- FORM TAMBAH ADMIN --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-plus text-red-600"></i> Tambah Admin
                    </h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- GRID 2 COLUMN --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @error('nama_lengkap')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username (Email)</label>
                                <input type="email" name="username" value="{{ old('username') }}"
                                    placeholder="email@domain.com"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @error('username')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Default</label>
                                <input type="password" name="password" placeholder="Default: 123456"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Konfirmasi</label>
                                <input type="password" name="password_confirmation" placeholder="Default: 123456"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Handphone --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Handphone</label>
                                <input type="text" name="handphone" value="{{ old('handphone') }}" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                @error('handphone')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Alamat (FULL WIDTH) --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                                <textarea name="alamat" rows="3" placeholder="Alamat lengkap admin"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        {{-- ACTION --}}
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL ADMIN --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-table text-red-600"></i> Data Admin
                    </h2>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Username</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $admin)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                        <td class="px-4 py-3 text-center">
                                            {{ $admins->firstItem() + $loop->index }}
                                        </td>

                                        <td class="px-4 py-3">{{ $admin->nama_lengkap }}</td>
                                        <td class="px-4 py-3">{{ $admin->username }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">

                                                {{-- EDIT --}}
                                                <button
                                                    class="btn-edit inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-amber-50 text-amber-700 hover:bg-amber-100"
                                                    data-id="{{ $admin->id }}" data-nama="{{ $admin->nama_lengkap }}"
                                                    data-username="{{ $admin->username }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                {{-- HAPUS --}}
                                                <button
                                                    class="btn-hapus inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-red-50 text-red-600 hover:bg-red-100"
                                                    data-id="{{ $admin->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                        <p class="text-sm text-slate-500">
                            Menampilkan
                            <b class="text-slate-700">{{ $admins->firstItem() }}</b> –
                            <b class="text-slate-700">{{ $admins->lastItem() }}</b>
                            dari <b class="text-slate-700">{{ $admins->total() }}</b> data
                        </p>

                        <div class="pagination-wrapper">
                            {{ $admins->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- MODAL EDIT ADMIN --}}
        <div id="modalEdit"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-edit text-red-600"></i> Edit Admin
                    </h3>
                    <button onclick="closeEdit()"
                        class="text-slate-400 hover:text-slate-600 transition text-lg leading-none">✕</button>
                </div>

                {{-- FORM --}}
                <form id="formEdit" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap2" id="editNama"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Nama lengkap admin">
                        </div>

                        {{-- Username --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username (Email)</label>
                            <input type="email" name="username2" id="editUsername"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="email@domain.com">
                        </div>

                        {{-- Password Baru --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Kosongkan jika tidak diubah">
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation2"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Ulangi password baru">
                        </div>

                        {{-- Handphone --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Handphone</label>
                            <input type="text" name="handphone2"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="08912345456">
                        </div>

                        {{-- Alamat (FULL WIDTH) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                            <textarea name="alamat2" id="editAlamat" rows="3"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Alamat lengkap admin"></textarea>
                        </div>

                    </div>

                    {{-- INFO --}}
                    <p class="text-xs text-slate-500 mt-3 flex items-center gap-1.5">
                        <i class="fas fa-info-circle"></i>
                        Kosongkan password jika tidak ingin mengubah password admin.
                    </p>

                    {{-- ACTION --}}
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeEdit()"
                            class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                            <i class="fas fa-times"></i> Batal
                        </button>

                        <button type="button" onclick="openConfirmSuperAdmin()"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL KONFIRMASI SUPER ADMIN --}}
        <div id="modalConfirmSuper"
            class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-red-600"></i> Konfirmasi Super Admin
                    </h3>
                </div>

                {{-- BODY --}}
                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-600">
                        Masukkan <b>Password Super Admin</b> untuk menyimpan perubahan data admin.
                    </p>

                    <input type="password" id="passwordSuperAdmin"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                        placeholder="Password Super Admin">
                </div>

                {{-- ACTION --}}
                <div class="flex justify-end gap-3 px-6 pb-6">
                    <button onclick="closeConfirmSuper()"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        Batal
                    </button>
                    <button onclick="submitEditWithSuperAdmin()"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                        <i class="fas fa-lock"></i> Konfirmasi &amp; Simpan
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS --}}
        <div id="modalHapus"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-red-600"></i> Konfirmasi Super Admin
                    </h3>
                </div>

                <form id="formHapus" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('DELETE')

                    <p class="text-sm text-slate-600">
                        Masukkan <b>password Super Admin</b> untuk menghapus data ini.
                    </p>

                    <input type="password" name="password_super_admin" placeholder="Password Super Admin"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeHapus()"
                            class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                            Batal
                        </button>
                        <button
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SCRIPT --}}
        <script>
            const modalEdit = document.getElementById('modalEdit');
            const modalHapus = document.getElementById('modalHapus');

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.onclick = () => {
                    modalEdit.classList.remove('hidden');
                    document.getElementById('editNama').value = btn.dataset.nama;
                    document.getElementById('editUsername').value = btn.dataset.username;
                    document.getElementById('formEdit').action = `/update/${btn.dataset.id}`;
                }
            });

            document.querySelectorAll('.btn-hapus').forEach(btn => {
                btn.onclick = () => {
                    modalHapus.classList.remove('hidden');
                    document.getElementById('formHapus').action = `/destroy/${btn.dataset.id}`;
                }
            });

            function closeEdit() {
                modalEdit.classList.add('hidden');
            }

            function closeHapus() {
                modalHapus.classList.add('hidden');
            }
        </script>

        <script>
            const modalConfirmSuper = document.getElementById('modalConfirmSuper');
            const formEdit = document.getElementById('formEdit');

            function openConfirmSuperAdmin() {
                modalConfirmSuper.classList.remove('hidden');
            }

            function closeConfirmSuper() {
                modalConfirmSuper.classList.add('hidden');
                document.getElementById('passwordSuperAdmin').value = '';
            }

            function submitEditWithSuperAdmin() {
                const password = document.getElementById('passwordSuperAdmin').value;

                if (!password) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Kosong',
                        text: 'Masukkan password Super Admin terlebih dahulu'
                    });
                    return;
                }

                // inject password ke form edit
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'password_super_admin';
                input.value = password;
                formEdit.appendChild(input);

                formEdit.submit();
            }
        </script>
    </div>
@endsection
