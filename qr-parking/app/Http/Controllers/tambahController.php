<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\listNamaRequest;
use App\Http\Resources\nyubeResource;
use App\Http\Resources\UserResource;
use App\Models\nyubeUser;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class tambahController extends Controller
{
    use ApiResponse;
    /**
     * @param \App\Http\Requests\RegisterRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function tambahUser(listNamaRequest $request)
    {
        $request->validated();

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads/foto_user', 'public');
        }

        $user = nyubeUser::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'username' => $request->username,
            'foto' => $fotoPath,
            'password' => Hash::make($request->password),

        ]);

        return $this->successResponse(
            new nyubeResource($user),
            'Berhasil melakukan registrasi.',
            201
        );
    }


    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255',
            'password' => 'nullable|string|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Ambil data user berdasarkan ID
        $user = nyubeUser::findOrFail($id);

        // Hanya update jika field dikirim
        if ($request->has('nama')) {
            $user->nama = $request->nama;
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('uploads/foto_user', 'public');
            $user->foto = $fotoPath;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diupdate',
            'data' => $user,
        ]);
    }



    public function show()
    {
        $paket = nyubeUser::all();

        if ($paket->isEmpty()) {
            return $this->errorResponse('Tidak ada paket aktif yang ditemukan.', 404);
        }

        return $this->successResponse(nyubeResource::collection($paket), 'Berhasil mendapatkan daftar paket aktif.');
    }

    public function showPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 5); // default 5 per halaman
        $users = nyubeUser::paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }


    public function showId(string $id)
    {
        $topups = nyubeUser::where('id', $id)->get();

        if ($topups->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }
        return $this->successResponse([
            'data' => $topups,
        ], 'Berhasil mendapatkan data.');
    }

    public function login(LoginRequest $request)
    {
        $user = nyubeUser::where('username', $request->username)->first();
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorResponse("Username atau password salah.", 400);
        }
        // dd(new nyubeResource($user));
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->successResponse([
            'user' => new nyubeResource($user),
            'token' => $token
        ], 'Login Succesfull', 201);
    }

    public function destroy($id)
    {
        $kendaraan = nyubeUser::find($id);

        if (!$kendaraan) {
            return $this->errorResponse('Kendaraan tidak ditemukan.', 404);
        }

        $kendaraan->delete();

        return $this->successResponse(null, 'Kendaraan berhasil dihapus.');
    }
}
