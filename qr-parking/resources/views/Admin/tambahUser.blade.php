@extends('app')

@section('page-title', 'Kelola User')
@section('page-subtitle', 'Tambah dan kelola akun user aplikasi e-Parkir')

@section('content')
    <div class="space-y-6">

        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{!! session('success') !!}",
                    timer: 2500,
                    showConfirmButton: false,
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{!! $errors->first() !!}",
                });
            </script>
        @endif

        {{-- PAGE HEADER --}}
        <div
            class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-plus text-red-600"></i>
                    Kelola User
                </h1>
                <p class="text-sm text-slate-500 mt-1">Tambahkan dan kelola akun user aplikasi parkir</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- FORM SECTION --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user text-red-600"></i>
                        Form Tambah User
                    </h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.storeUser') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('nama_lengkap')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Photo</label>
                                <input type="file" name="photo"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('photo')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                    <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih jenis
                                        kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Handphone</label>
                                <input type="text" name="handphone" value="{{ old('handphone') }}"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('handphone')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email (Username)</label>
                                <input type="email" name="username" value="{{ old('username') }}"
                                    placeholder="Tuliskan alamat email aktif"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('username')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                                <input type="password" name="password" placeholder="Minimal 6 karakter"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            </div>

                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                            <textarea name="alamat" placeholder="Masukkan alamat lengkap"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                rows="4">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- DATA TABLE SECTION --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-table text-red-600"></i>
                        Data Tabel Users
                    </h2>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Password
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-md text-blue-600 hover:bg-blue-50 toggle-action"
                                                    data-id="{{ $user->id_users }}" title="Tampilkan aksi">
                                                    <i class="fas fa-plus-circle"></i>
                                                </button>
                                                <span>{{ $loop->iteration }}.</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">{{ $user->nama_lengkap }}</td>
                                        <td class="px-4 py-3">{{ $user->username }}</td>
                                        <td class="px-4 py-3">********</td>
                                    </tr>
                                    <tr class="action-row hidden" id="action-row-{{ $user->id_users }}">
                                        <td colspan="4" class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" onclick="openEditModal({{ $user->id_users }})"
                                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-amber-50 text-amber-700 hover:bg-amber-100 btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button"
                                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-red-50 text-red-600 hover:bg-red-100 btn-hapus"
                                                    data-id="{{ $user->id_users }}"
                                                    data-nama="{{ $user->nama_lengkap }}">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-slate-400">
                                            <i class="fas fa-inbox text-2xl mb-2 block"></i>
                                            Belum ada data user.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4">
                        <div class="text-sm text-slate-500">
                            Showing 1 to {{ $users->count() }} of {{ $users->total() ?? $users->count() }} entries
                        </div>
                        <div class="flex space-x-2">
                            {{ $users->links() ?? '' }}
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-slate-500">
                        <p>
                            Mohon berhati-hati jika ingin menghapus data user, pastikan tidak sedang digunakan untuk login
                            atau proses lainnya!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- MODAL EDIT USER --}}
    <div id="editModal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl relative my-auto">
                <button type="button" onclick="closeEditModal()"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-semibold mb-4 text-slate-900 border-b border-slate-100 pb-2">Edit Data User</h2>

                <form id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" id="edit_id_users" name="id_users">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" id="edit_nama_lengkap" name="nama_lengkap1" value=""
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                            <select id="edit_jenis_kelamin" name="jenis_kelamin2"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                <option value="" disabled>Pilih jenis kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No Handphone</label>
                            <input id="edit_no_hp" type="text" name="handphone1" value=""
                                placeholder="Contoh: 081234567890"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email (Username)</label>
                            <input id="edit_email" type="email" name="username1" value=""
                                placeholder="Tuliskan alamat email aktif"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                            <input id="edit_password" type="password" name="password1"
                                placeholder="Kosongkan jika tidak diubah"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('password1')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password1_confirmation" placeholder="Ulangi password baru"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                        <textarea id="edit_alamat" name="alamat2" placeholder="Masukkan alamat lengkap"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                            rows="4"></textarea>
                    </div>

                    <div class="flex justify-end items-center gap-3 mt-6">
                        <button type="button" onclick="closeEditModal()"
                            class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                            Batal
                        </button>
                        <button type="button" onclick="openConfirmEdit()"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI PASSWORD --}}
    <div id="modalConfirmEdit" class="hidden fixed inset-0 z-[110] overflow-y-auto bg-slate-900/60 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md my-auto">
                <h2 class="text-lg font-bold mb-2 text-slate-900">Konfirmasi Aksi</h2>
                <p class="mb-3 text-sm text-slate-600">Masukkan password admin (akun yang sedang login) untuk melanjutkan:
                </p>
                <input type="password" id="editAdminPassword" placeholder="Password admin"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition mb-4">

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeConfirmEdit()"
                        class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                        Batal
                    </button>
                    <button type="button" onclick="submitEditWithPassword()"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                        <i class="fas fa-lock"></i> Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div id="modalHapus" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-sm">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md my-auto">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-red-600"></i> Konfirmasi Hapus
                    </h3>
                </div>

                <form id="formHapus" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('DELETE')

                    <p class="text-sm text-slate-600">
                        Menghapus data <b id="hapusNama" class="text-slate-900"></b>.
                        Masukkan <b>password admin</b> untuk melanjutkan.
                    </p>

                    <input type="password" name="password_super_admin" placeholder="Password admin"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeHapus()"
                            class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                            Batal
                        </button>
                        <button
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        const USER_EDIT_URL = "{{ url('edit-user') }}";
        const USER_GET_URL = "{{ url('get-user') }}";
        const USER_DELETE_URL = "{{ url('hapus-staff') }}";

        const editModal = document.getElementById('editModal');
        const modalConfirmEdit = document.getElementById('modalConfirmEdit');
        const modalHapus = document.getElementById('modalHapus');

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-action').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const row = document.getElementById(`action-row-${id}`);
                    if (row) row.classList.toggle('hidden');
                });
            });

            document.querySelectorAll('.btn-hapus').forEach(btn => {
                btn.addEventListener('click', () => {
                    modalHapus.classList.remove('hidden');
                    document.getElementById('hapusNama').textContent = btn.dataset.nama ?? '';
                    document.getElementById('formHapus').action =
                        `${USER_DELETE_URL}/${btn.dataset.id}`;
                });
            });
        });

        function openEditModal(id) {
            const form = document.getElementById('editUserForm');
            editModal.classList.remove('hidden');
            form.action = `${USER_EDIT_URL}/${id}`;
            form.reset();

            fetch(`${USER_GET_URL}/${id}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('edit_nama_lengkap').value = user.nama_lengkap ?? '';
                    document.getElementById('edit_email').value = user.username ?? '';
                    document.getElementById('edit_no_hp').value = user.no_hp ?? '';
                    document.getElementById('edit_jenis_kelamin').value = user.jenis_kelamin ?? '';
                    document.getElementById('edit_alamat').value = user.alamat ?? '';
                    document.getElementById('edit_id_users').value = user.id_users ?? '';
                })
                .catch(error => {
                    console.error('Gagal mengambil data user:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil data user.'
                    });
                    closeEditModal();
                });
        }

        function closeEditModal() {
            editModal.classList.add('hidden');
        }

        function openConfirmEdit() {
            modalConfirmEdit.classList.remove('hidden');
        }

        function closeConfirmEdit() {
            modalConfirmEdit.classList.add('hidden');
            document.getElementById('editAdminPassword').value = '';
        }

        function submitEditWithPassword() {
            const password = document.getElementById('editAdminPassword').value;

            if (!password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Kosong',
                    text: 'Masukkan password admin terlebih dahulu'
                });
                return;
            }

            const form = document.getElementById('editUserForm');
            form.querySelectorAll('input[name="admin_password"]').forEach(el => el.remove());

            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'admin_password';
            input.value = password;
            form.appendChild(input);

            form.submit();
        }

        function closeHapus() {
            modalHapus.classList.add('hidden');
        }
    </script>
@endpush
