<?php

namespace App\Http\Controllers\ControllersWeb;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.dataUsersMaster', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|email|unique:admins,username',
            'password' => 'required|min:6|confirmed',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);

        Admin::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'handphone' => $request->handphone,
            'alamat' => $request->alamat,
        ]);

        return back()->with('success', 'Admin berhasil ditambahkan');
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'nama_lengkap2' => 'required|string|max:100',
            'username2' => 'required|email|unique:admins,username,' . $admin->id,
            'handphone2' => 'required',
            'alamat2' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'password_super_admin' => 'required'
        ]);

        $superAdmin = Auth::user();

        // VALIDASI ROLE
        if ($superAdmin->role !== 'admin_super') {
            return back()->withErrors([
                'password_super_admin' => 'Anda bukan Super Admin'
            ]);
        }

        // VALIDASI PASSWORD SUPER ADMIN
        if (!Hash::check($request->password_super_admin, $superAdmin->password)) {
            return back()->withErrors([
                'password_super_admin' => 'Password Super Admin salah'
            ]);
        }

        // UPDATE DATA
        $admin->nama_lengkap = $request->nama_lengkap2;
        $admin->username = $request->username2;
        $admin->handphone = $request->handphone2;
        $admin->alamat = $request->alamat2;

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

        // (Optional) validasi super admin di sini

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

        return redirect()->route('Admin.createStaff')->with('success', 'Data staff berhasil disimpan!');
    }


    public function createUser()
    {
        $users = User::paginate(10); // contoh pagination
        return view('admin.tambahUser', compact('users'));
    }

    public function createStaff()
    {
        $users = User::where('role', 'staff')->paginate(10); // hanya staff
        return view('Admin.kelolaStaffMaster', compact('users'));
    }

    // UserController.php



    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'handphone' => 'required|string|max:20',
            'username' => 'required|email|unique:users,username',
            'password' => 'required|string|min:6',
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

        return redirect()->route('Admin.createUser')->with('success', 'User berhasil ditambahkan.');
    }




    public function editUser(Request $request, $id)
    {
        // dd($request->all());
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

        // ✅ Ambil data admin dari session
        $admin = session('adminData');

        if (!$admin) {
            return redirect()->back()->withErrors(['admin_password' => 'Admin tidak ditemukan'])->withInput();
        }

        // ✅ Cek password admin
        if (!Hash::check($request->admin_password, $admin->password)) {
            return redirect()->back()->withErrors(['admin_password' => 'Password admin tidak valid'])->withInput();
        }

        // ✅ Update data user
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

        return redirect()->route('Admin.createStaff')->with('success', 'Data user berhasil diperbarui.');
    }


    public function destroyStaff($id)
    {
        $data = User::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }



    // UserController.php

    public function getUser($id_users)
    {
        $user = User::find($id_users); // Pastikan model User sesuai
        return response()->json($user);
    }
}
