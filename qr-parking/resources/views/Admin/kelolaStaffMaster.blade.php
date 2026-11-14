@extends('app')

@section('page-title', 'Kelola Staff')
@section('page-subtitle', 'Tambah dan kelola akun staff aplikasi e-Parkir')

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

        {{-- PAGE HEADER --}}
        <div
            class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-users text-red-600"></i>
                    Kelola Staff
                </h1>
                <p class="text-sm text-slate-500 mt-1">Tambahkan dan kelola akun staff aplikasi parkir</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- FORM SECTION --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-plus text-red-600"></i>
                        Form Tambah Staff
                    </h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.storeStaff') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                    required />
                                @error('nama_lengkap')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Photo
                                </label>
                                <input type="file" name="photo"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('photo')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_kelamin"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                    required>
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
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Handphone
                                </label>
                                <input type="text" name="handphone" value="{{ old('handphone') }}"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                                @error('handphone')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Username (Email) <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="username" value="{{ old('username') }}"
                                    placeholder="Tuliskan alamat email aktif"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                    required />
                                @error('username')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Password Default <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" value="" placeholder="Password default: 123456"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                    required />
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Password Confirmation <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" value=""
                                    placeholder="Password default: 123456"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                    required />
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Alamat
                            </label>
                            <textarea name="alamat" placeholder="Masukkan alamat lengkap"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                                rows="4">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                            <a href="{{ url()->previous() }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- DATA TABLE SECTION --}}
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-table text-red-600"></i>
                        Data Tabel Staffs
                    </h2>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Username
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide">Password
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $Staff)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50/70 transition">
                                        <td class="px-4 py-3 text-center">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-4 py-3">{{ $Staff->nama_lengkap }}</td>
                                        <td class="px-4 py-3">{{ $Staff->username }}</td>
                                        <td class="px-4 py-3">********</td>

                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Tombol Edit -->
                                                <a href="javascript:void(0);"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-amber-50 text-amber-700 hover:bg-amber-100"
                                                    title="Edit" data-user-id="{{ $Staff->id_users }}"
                                                    onclick="openEditModal(this)">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('admin.destroyStaff', $Staff->id_users) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus staff ini?')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold transition bg-red-50 text-red-600 hover:bg-red-100"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-4">
                        <div class="text-sm text-slate-500">
                            Showing 1 to {{ $users->count() }} of {{ $users->total() ?? $users->count() }} entries
                        </div>
                        <div class="flex space-x-2">
                            {{ $users->links() }}
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-slate-500">
                        <p>
                            Mohon berhati-hati jika ingin menghapus data kendaraan usahakan sebelum melakukan peng-inputan
                            data parkir, karena akan menyebabkan data error!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div id="editModal"
            class="hidden fixed inset-0 z-[999] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl relative overflow-y-auto max-h-[95vh]">
                <!-- Tombol Close -->
                <button onclick="closeEditModal()"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Judul -->
                <h2 class="text-xl font-semibold mb-4 text-slate-900 border-b border-slate-100 pb-2">Edit
                    Data User
                </h2>

                <form id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" id="edit_id_users" name="id_users">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama
                                Lengkap</label>
                            <input type="text" id="edit_nama_lengkap" name="nama_lengkap1" value=""
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('nama_lengkap')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis
                                Kelamin</label>
                            <select name="jenis_kelamin2"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition">
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih
                                    jenis
                                    kelamin</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No
                                Handphone</label>
                            <input id="edit_no_hp" type="text" name="handphone1"
                                value="{{ old('handphone') }}" placeholder="Contoh: 081234567890"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('handphone')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email
                                (Username)
                            </label>
                            <input id="edit_email" type="email" name="username1" value="{{ old('username') }}"
                                placeholder="Tuliskan alamat email aktif"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('username')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                            <input id="edit_password" type="password" name="password1"
                                placeholder="Password default: 123456"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('password1')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi
                                Password</label>
                            <input type="password" name="password1_confirmation" placeholder="Password default: 123456"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition" />
                            @error('password1_confirmation')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                        <textarea id="edit_alamat" name="alamat2" placeholder="Masukkan alamat lengkap"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition"
                            rows="4">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end items-center gap-3 mt-6">
                        <div x-data="{ showModal: false, adminPassword: '' }">
                            <!-- Tombol Update -->
                            <button type="button" @click="showModal = true"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                <i class="fas fa-save"></i> Update
                            </button>

                            <!-- Modal Konfirmasi Password Admin -->
                            <div x-show="showModal"
                                class="fixed inset-0 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm z-50"
                                x-transition>
                                <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                                    <h2 class="text-xl font-semibold mb-4 text-slate-900">Konfirmasi Aksi
                                    </h2>
                                    <p class="mb-3 text-sm text-slate-600">Masukkan password admin untuk
                                        melanjutkan:
                                    </p>
                                    <input type="password" x-model="adminPassword" name="admin_password"
                                        placeholder="Password admin"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none transition mb-4">

                                    <div class="flex justify-end gap-3">
                                        <!-- Tombol batal: hanya tutup modal -->
                                        <button type="button" @click="showModal = false"
                                            class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 inline-flex items-center gap-2 rounded-xl text-sm font-semibold px-4 py-2.5 transition">
                                            Batal
                                        </button>

                                        <!-- Tombol lanjut -->
                                        <button type="button"
                                            @click="if (adminPassword.length > 0) $refs.submitButton.click()"
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                                            Lanjutkan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol submit form tersembunyi -->
                            <button type="submit" x-ref="submitButton" class="hidden"></button>
                        </div>

                        <!-- Tombol batal luar (misalnya cancel edit form) -->
                        <a href="javascript:void(0);"
                            class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 shadow-sm transition">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

<script>
    function openEditModal(element) {
        const id = element.getAttribute('data-user-id');

        // Tampilkan modal
        document.getElementById('editModal').classList.remove('hidden');

        // Update form action
        const form = document.getElementById('editUserForm');
        form.action = `/edit-user/${id}`;

        // Kosongkan dulu input agar tidak berisi data lama
        form.reset();

        // Ambil data user
        fetch(`/get-user/${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Data tidak ditemukan');
                return response.json();
            })
            .then(user => {
                // Isi data ke form
                document.getElementById('edit_nama_lengkap').value = user.nama_lengkap ?? '';
                document.getElementById('edit_email').value = user.username ?? '';
                document.getElementById('edit_no_hp').value = user.no_hp ?? '';
                document.querySelector('select[name="jenis_kelamin2"]').value = user.jenis_kelamin ?? '';
                document.getElementById('edit_alamat').value = user.alamat ?? '';
                document.getElementById('edit_id_users').value = user.id_users ?? '';
            })
            .catch(error => {
                console.error('Gagal mengambil data:', error);
                alert('Gagal mengambil data user.');
                closeEditModal();
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
