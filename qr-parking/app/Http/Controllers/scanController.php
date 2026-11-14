<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\listNamaRequest;
use App\Http\Resources\nyubeResource;
use App\Http\Resources\UserResource;
use App\Models\DataParkir;
use App\Models\nyubeUser;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class scanController extends Controller
{
    use ApiResponse;
    /**
     * Registrasi
     * @param \App\Http\Requests\RegisterRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */





    /**
     * cekStatus
     * @param \App\Http\Requests\LoginRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function statusCheck($user_id)
    {
        // Ambil user berdasarkan id_users, bukan id
        $user = User::where('id_users', $user_id)->first();

        if (!$user) {
            return $this->errorResponse("User tidak ditemukan.", 404);
        }

        // Cek status parkir user
        $dataParkir = DataParkir::where('user_id', $user_id)
            ->where('status', 'Belum Dibayar')
            ->first();

        if (!$dataParkir) {
            return $this->errorResponse("Tidak ada proses aktif untuk user ini.", 404);
        }

        return $this->successResponse([
            'user' => new UserResource($user),
            'status' => $dataParkir->status,
            'data_parkir' => $dataParkir,
        ], 'Status ditemukan: Sedang Diproses', 200);
    }

    public function validasi(Request $request)
    {
        $request->validate([
            'kode' => 'required|string',
            'signature' => 'required|string',
        ]);

        $kode = $request->kode;
        $signature = $request->signature;

        // Pakai secret key statis khusus QR
        $secretKey = env('QR_SECRET_STATIC', 'ganparkingRAHASIAkhususKARTU');
        $expectedSignature = hash_hmac('sha256', "GANPARKINGKARTU", $secretKey);

        // Cek signature cocok?
        if ($signature !== $expectedSignature) {
            return response()->json(['message' => 'Signature tidak valid'], 400);
        }

        // Cek kode benar
        if ($kode !== 'GANPARKING') {
            return response()->json(['message' => 'Kode tidak valid'], 400);
        }

        $user = Auth::user();



        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 401);
        }

        return response()->json([
            'message' => 'QR valid',
            'data' => [
                'user_id' => $user->id_users,
            ],
        ]);
    }
}
