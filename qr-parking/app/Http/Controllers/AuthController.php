<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\listNamaRequest;
use App\Http\Requests\listNamaRequestUpdate;
use App\Http\Resources\nyubeResource;
use App\Http\Resources\UserResource;
use App\Models\nyubeUser;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Registrasi
     * @param \App\Http\Requests\RegisterRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function register(RegisterRequest $request)
    {
        $request->validated();

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_users')) {
            $fotoPath = $request->file('foto_users')->store('uploads/foto_user', 'public');
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'foto_users' => $fotoPath,
        ]);

        return $this->successResponse(
            new UserResource($user),
            'Berhasil melakukan registrasi.',
            201
        );
    }



    /**
     * Login
     * @param \App\Http\Requests\LoginRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse("Username atau password salah.", 400);
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login berhasil sebagai ' . $user->role, 201);
    }



    /**
     * Logout
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(message: 'Berhasil Keluar.');
    }

    /**
     * Mendapatkan data user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return $this->successResponse(Auth::user(), message: 'Berhasil mengambil data.');
    }
}
