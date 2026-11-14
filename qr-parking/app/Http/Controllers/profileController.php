<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\listNamaRequest;
use App\Http\Requests\listNamaRequestUpdate;
use App\Http\Resources\nyubeResource;
use App\Http\Resources\UserResource;
use App\Models\Denda;
use App\Models\nyubeUser;
use App\Models\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class profileController extends Controller
{
    use ApiResponse;
    /**
     * @param \App\Http\Requests\RegisterRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function updateUserParking(listNamaRequestUpdate $request, $id_users)
    {
        $data = $request->validated();

        $user = User::findOrFail($id_users);

        if (isset($data['nama_lengkap'])) {
            $user->nama_lengkap = $data['nama_lengkap'];
        }

        if (isset($data['username'])) {
            $user->username = $data['username'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('foto_users')) {
            $fotoPath = $request->file('foto_users')->store('uploads/foto_user', 'public');
            $user->foto_users = $fotoPath;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil diupdate',
            'data' => $user,
        ]);
    }


    public function showIdUser(string $id)
    {
        $topups = User::where('id_users', $id)->get();

        if ($topups->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }
        return $this->successResponse([
            'data' => $topups,
        ], 'Berhasil mendapatkan data.');
    }

    public function showDendaPendingByUserId($id_users)
    {
        // Ambil semua denda dengan status 'pending' milik user
        $dendaList = Denda::where('id_users', $id_users)
            ->where('status', 'pending') // hanya denda yang belum dibayar
            ->with(['kendaraan']) // relasi opsional jika diperlukan
            ->orderBy('waktu_pelanggaran', 'desc')
            ->get();

        if ($dendaList->isEmpty()) {
            return response()->json([
                'status' => 'clear',
                'message' => 'Tidak ada denda pending untuk user ini.',
                'data' => [],
            ], 200);
        }

        // Format response
        $data = $dendaList->map(function ($item) {
            return [
                'id'                => $item->id,
                'id_users'          => $item->id_users,
                'id_kendaraan'      => $item->id_kendaraan,
                'plat_nomor'        => $item->plat_nomor,
                'jenis_pelanggaran' => $item->jenis_pelanggaran,
                'waktu_pelanggaran' => $item->waktu_pelanggaran,
                'denda'             => $item->denda,
                'status'            => $item->status,
                'keterangan'        => $item->keterangan,
            ];
        });

        return response()->json([
            'status' => 'denda',
            'message' => 'Berhasil mendapatkan denda pending user.',
            'data' => $data,
        ], 200);
    }

    public function bayarDenda(Request $request)
    {
        $request->validate([
            'id_users' => 'required|integer',
            'id_denda' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->id_users);
            $denda = Denda::findOrFail($request->id_denda);

            // Pastikan denda masih pending
            if ($denda->status !== 'pending') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Denda sudah dibayar atau tidak berlaku.',
                ], 400);
            }

            // Cek apakah saldo cukup
            if ($user->saldo < $denda->denda) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Saldo tidak mencukupi untuk membayar denda.',
                ], 400);
            }

            // Kurangi saldo user
            $user->saldo -= $denda->denda;
            $user->save();

            // Update status denda jadi success
            $denda->status = 'dibayar';
            $denda->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Denda berhasil dibayar dan saldo telah diperbarui.',
                'data' => [
                    'sisa_saldo' => $user->saldo,
                    'status_denda' => $denda->status,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses pembayaran.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
