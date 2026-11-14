<?php

namespace App\Http\Controllers\ControllersWeb;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(5);
        return view('Admin.dataUsersMaster', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'  => 'required|string|max:200',
            'username'      => 'required|email|unique:admin,username',
            'password'      => 'required|min:6|confirmed',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'handphone'     => 'required|string|max:20',
            'alamat'        => 'required|string|max:200',
        ]);

        Admin::create([
            'nama_lengkap'  => $request->nama_lengkap,
            'username'      => $request->username,
            'password'      => Hash::make($request->password),
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp'         => $request->handphone,
            'alamat'        => $request->alamat,
            'role'          => 'admin',
        ]);

        return back()->with('success', 'Admin berhasil ditambahkan');
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'nama_lengkap2' => 'required|string|max:200',
            'username2'     => 'required|email|unique:admin,username,' . $admin->id_admin . ',id_admin',
            'handphone2'    => 'required|string|max:20',
            'alamat2'       => 'required|string|max:200',
            'password'      => 'nullable|min:6|confirmed',
            'password_super_admin' => 'required',
        ]);

        $superAdmin = session('adminData');

        // VALIDASI ROLE
        if (!$superAdmin || $superAdmin->role !== 'admin_super') {
            return back()->withErrors([
                'password_super_admin' => 'Anda bukan Super Admin'
            ])->withInput();
        }

        // VALIDASI PASSWORD SUPER ADMIN (akun yang sedang login)
        if (!Hash::check($request->password_super_admin, $superAdmin->password)) {
            return back()->withErrors([
                'password_super_admin' => 'Password Super Admin salah'
            ])->withInput();
        }

        // UPDATE DATA
        $admin->nama_lengkap = $request->nama_lengkap2;
        $admin->username     = $request->username2;
        $admin->no_hp        = $request->handphone2;
        $admin->alamat       = $request->alamat2;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return back()->with('success', 'Data admin berhasil diperbarui');
    }

    public function destroy(Request $request, Admin $admin)
    {
        $request->validate([
            'password_super_admin' => 'required'
        ]);

        $superAdmin = session('adminData');

        if (!$superAdmin || $superAdmin->role !== 'admin_super') {
            return back()->withErrors(['password_super_admin' => 'Anda bukan Super Admin']);
        }

        // Validasi password sesuai akun yang sedang login
        if (!Hash::check($request->password_super_admin, $superAdmin->password)) {
            return back()->withErrors(['password_super_admin' => 'Password Super Admin salah']);
        }

        // Cegah menghapus akun yang sedang dipakai login
        if ($superAdmin->id_admin === $admin->id_admin) {
            return back()->withErrors(['password_super_admin' => 'Akun yang sedang login tidak dapat dihapus']);
        }

        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus');
    }


    public function storeStaff(Request $request)
    {
        // Validasi data
        $request->validate([
            'nama_lengkap'        => 'required|string|max:255',
            'photo'               => 'nullable|image|max:2048',
            'jenis_kelamin'       => 'required|in:Perempuan,Laki-laki',
            'handphone'           => 'nullable|string|max:20',
            'username'            => 'required|email|unique:users,username',
            'password'            => 'required|string|min:6|confirmed',
            'alamat'              => 'nullable|string|max:500',
        ]);

        // Simpan data
        $staff = new User();
        $staff->nama_lengkap   = $request->nama_lengkap;
        $staff->jenis_kelamin  = $request->jenis_kelamin;
        $staff->no_hp          = $request->handphone;
        $staff->username       = $request->username;
        $staff->password       = bcrypt($request->password);
        $staff->alamat         = $request->alamat;
        $staff->role           = 'staff';

        // Upload photo (jika ada)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/images'), $filename);
            $staff->foto_users = 'storage/images/' . $filename;
        }

        $staff->save();

        return redirect()->route('admin.createStaff')->with('success', 'Data staff berhasil disimpan!');
    }


    public function createUser()
    {
        $users = User::paginate(10); // contoh pagination
        return view('Admin.tambahUser', compact('users'));
    }

    public function createStaff()
    {
        $users = User::where('role', 'staff')->paginate(10); // hanya staff
        return view('Admin.kelolaStaffMaster', compact('users'));
    }


    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'handphone' => 'required|string|max:20',
            'username' => 'required|email|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'alamat' => 'nullable|string|max:1000',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_photos', 'public');
        }

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'foto_users' => $photoPath,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->handphone,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'alamat' => $request->alamat,
            'role' => "user",
        ]);

        return redirect()->route('admin.createUser')->with('success', 'User berhasil ditambahkan.');
    }


    public function editUser(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap1' => 'sometimes|required|string|max:255',
            'photo' => 'sometimes|nullable|image|max:2048',
            'jenis_kelamin2' => 'sometimes|required|in:Laki-laki,Perempuan',
            'handphone1' => 'sometimes|nullable|string|max:20',
            'username1' => 'sometimes|required|email|unique:users,username,' . $id . ',id_users',
            'password1' => 'sometimes|nullable|string|min:6|confirmed',
            'alamat2' => 'sometimes|nullable|string|max:1000',
            'admin_password' => 'required|string',
        ]);

        // Ambil data admin dari session
        $admin = session('adminData');

        if (!$admin) {
            return redirect()->back()->withErrors(['admin_password' => 'Admin tidak ditemukan'])->withInput();
        }

        // Cek password admin (akun yang sedang login)
        if (!Hash::check($request->admin_password, $admin->password)) {
            return redirect()->back()->withErrors(['admin_password' => 'Password admin tidak valid'])->withInput();
        }

        // Update data user
        $user = User::findOrFail($id);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user_photos', 'public');
            $user->foto_users = $photoPath;
        }

        $user->nama_lengkap = $request->nama_lengkap1;
        $user->jenis_kelamin = $request->jenis_kelamin2;
        $user->no_hp = $request->handphone1;
        $user->username = $request->username1;
        $user->alamat = $request->alamat2;

        if ($request->filled('password1')) {
            $user->password = bcrypt($request->password1);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
    }


    public function destroyStaff(Request $request, $id)
    {
        $request->validate([
            'password_super_admin' => 'required'
        ]);

        $admin = session('adminData');

        if (!$admin || !Hash::check($request->password_super_admin, $admin->password)) {
            return back()->withErrors(['password_super_admin' => 'Password admin salah'])->withInput();
        }

        $data = User::findOrFail($id);
        $data->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }


    public function getUser($id_users)
    {
        $user = User::find($id_users);
        return response()->json($user);
    }
}
