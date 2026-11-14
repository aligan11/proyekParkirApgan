<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\topUpStoreRequest;
use App\Http\Resources\isiSaldoResource;
use App\Http\Resources\LeaveRequestResource;
use App\Http\Resources\TopupResource;
use App\Jobs\DeletePendingTopup;
use App\Models\DataParkir;
use App\Models\LeaveRequest;
use App\Models\PaketTopup;
use App\Models\Topup;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use PHPUnit\Framework\Constraint\IsEmpty;

class TopupController extends Controller
{

    use ApiResponse;



    public function store(topUpStoreRequest $request)
    {
        $request->validated();

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $totalDibayar = $request->nominal + $request->biaya_admin;
        $orderId = 'TOPUP-' . $user->id . '-' . Str::random(10) . '-' . time();
        $serverKey = config('services.midtrans.server_key');

        $topup = new Topup();
        $topup->user_id = $user->id_users;
        $topup->order_id = $orderId;
        $topup->nominal = $request->nominal;
        $topup->biaya_admin = $request->biaya_admin;
        $topup->total_dibayar = $totalDibayar;
        $topup->status = 'pending';
        $topup->metode_pembayaran = $request->metode_pembayaran;

        try {
            if ($request->metode_pembayaran) {
                // ✅ Core API: jika metode pembayaran ada
                $body = [
                    "payment_type" => $request->metode_pembayaran,
                    "transaction_details" => [
                        "order_id" => $orderId,
                        "gross_amount" => $totalDibayar,
                    ],
                    "customer_details" => [
                        "first_name" => $user->name,
                        "email" => $user->email,
                    ]
                ];

                if ($request->metode_pembayaran === 'gopay') {
                    $body['gopay'] = [
                        "enable_callback" => true,
                        "callback_url" => "https://your-app.com/payment/finish"
                    ];
                }


                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.sandbox.midtrans.com/v2/charge', $body);

                $responseData = $response->json();

                if (!$response->successful()) {
                    return response()->json([
                        'message' => 'Gagal membuat transaksi di Midtrans',
                        'error' => $responseData,
                    ], $response->status());
                }

                $actions = collect($responseData['actions'] ?? []);
                $imageUrl = 'https://api.sandbox.midtrans.com/v2/qris/' . $responseData['transaction_id'] . '/qr-code';
                $redirectUrl = $responseData['redirect_url'] ?? null;

                if (empty($redirectUrl)) {
                    // fallback untuk Gopay, QRIS, dll yang pakai actions
                    $actions = collect($responseData['actions'] ?? []);
                    $redirectUrl = $actions->firstWhere('name', 'deeplink-redirect')['url'] ?? null;
                }

                $imageUrl = 'https://api.sandbox.midtrans.com/v2/qris/' . ($responseData['transaction_id'] ?? '') . '/qr-code';
            } else {
                // ✅ Snap API: jika metode tidak diisi
                $snapPayload = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $totalDibayar,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'callbacks' => [
                        'finish' => 'https://your-app.com/payment/finish?order_id=' . $orderId,
                    ]
                ];

                $snapResponse = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $snapPayload);

                $snapData = $snapResponse->json();

                if (!$snapResponse->successful()) {
                    return response()->json([
                        'message' => 'Gagal membuat transaksi Snap di Midtrans',
                        'error' => $snapData,
                    ], $snapResponse->status());
                }

                $snapUrl = $snapData['redirect_url'] ?? null;
            }

            $topup->save();
            DeletePendingTopup::dispatch($topup->id)->delay(now()->addMinutes(5));

            return response()->json([
                'message' => 'Top-up transaction initiated successfully',
                'data' => $topup,
                'midtrans_redirect_url' => $redirectUrl ?? $snapUrl ?? null,
                'midtrans_qr_image_url' => $imageUrl ?? null,
                'midtrans_snap_url' => $snapUrl ?? null,
                'payment_type' => $request->metode_pembayaran,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Midtrans Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to initiate Midtrans transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deletePendingTopupForUser(Request $request)
    {
        $user = Auth::user(); // Ambil user dari session login

        if (!$user) {
            return response()->json(['message' => 'User tidak terautentikasi'], 401);
        }

        // Hapus semua topup yang masih pending untuk user tersebut
        $deleted = Topup::where('user_id', $user->id_users)
            ->where('status', 'pending')
            ->delete();

        return response()->json([
            'message' => $deleted > 0
                ? "$deleted transaksi pending berhasil dihapus."
                : "Tidak ada transaksi pending yang ditemukan.",
            'deleted_count' => $deleted
        ]);
    }





    public function showAktif()
    {
        $paket = PaketTopup::where('is_aktif', 1)->get();

        if ($paket->isEmpty()) {
            return $this->errorResponse('Tidak ada paket aktif yang ditemukan.', 404);
        }

        return $this->successResponse(TopupResource::collection($paket), 'Berhasil mendapatkan daftar paket aktif.');
    }

    public function show(string $user_id)
    {
        // Ambil hanya topup dengan status 'success' milik user tertentu
        $topups = Topup::where('user_id', $user_id)
            ->where('status', 'success')
            ->get();

        if ($topups->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // Hitung total saldo hanya dari nominal topup yang 'success'
        $totalSaldo = $topups->sum(function ($item) {
            return (int) str_replace('.', '', $item->nominal);
        });

        return $this->successResponse([
            'data' => $topups,
            'saldo' => $totalSaldo,
        ], 'Berhasil mendapatkan data.');
    }

    public function showAll(Request $request, string $user_id)
    {
        // Jumlah minimal per halaman   
        $perPage = 3;

        // Pagination Topup user dengan status 'success'
        $topups = Topup::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        if ($topups->isEmpty()) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // Hitung total saldo dari nominal topup (hanya yang success)
        $totalSaldo = Topup::where('user_id', $user_id)
            ->where('status', 'success')
            ->get()
            ->sum(function ($item) {
                return (int) str_replace('.', '', $item->nominal);
            });

        return $this->successResponse([
            'data' => $topups->items(),   // Data per halaman
            'pagination' => [
                'current_page' => $topups->currentPage(),
                'last_page' => $topups->lastPage(),
                'per_page' => $topups->perPage(),
                'total' => $topups->total(),
            ],
            'saldo' => $totalSaldo,
        ], 'Berhasil mendapatkan data.');
    }




    public function CheckStatus(string $user_id)
    {
        $topup = Topup::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$topup) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // Jika kamu ingin cek apakah status-nya pending
        if ($topup->status === 'pending') {
            return $this->successResponse([
                'data' => $topup,
            ], 'Top-up terbaru masih pending.');
        }

        return $this->successResponse([
            'data' => $topup,
        ], 'Top-up terbaru ditemukan.');
    }


    public function handleNotification(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $data = $request->all();

        Log::info('📩 Notifikasi Midtrans Masuk', $data);

        $expectedSignature = hash("sha512", $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);
        if ($expectedSignature !== $data['signature_key']) {
            Log::warning("❌ Signature tidak cocok", [
                'expected' => $expectedSignature,
                'received' => $data['signature_key'],
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // === 1. Cek dan update TOPUP jika ada ===
        $order = Topup::where('order_id', $data['order_id'])->first();
        if ($order) {
            $order->metode_pembayaran = $data['payment_type'] ?? $order->metode_pembayaran;

            switch ($data['transaction_status']) {
                case 'settlement':
                case 'capture':
                    $order->status = 'success';
                    // ✅ Tambah saldo user
                    $user = $order->user; // pastikan relasi Topup::user() sudah ada
                    if ($user) {
                        $user->increment('saldo', $order->nominal);
                        Log::info("💰 Saldo user ID {$user->id_users} ditambahkan sebesar {$order->nominal}");

                        // ✅ Cek apakah ada denda yang masih pending
                        $pendingDenda = $user->denda()->where('status', 'pending')->first();

                        if ($pendingDenda) {
                            $saldoSekarang = $user->saldo;
                            $jumlahDenda = $pendingDenda->denda;

                            // Kurangi saldo jika mencukupi (atau tetap kurangi meskipun tidak cukup tergantung aturan bisnis Anda)
                            if ($saldoSekarang >= $jumlahDenda) {
                                $user->decrement('saldo', $jumlahDenda);

                                // Ubah status denda menjadi dibayar
                                $pendingDenda->status = 'dibayar';
                                $pendingDenda->save();

                                Log::info("⚠️ Denda ID {$pendingDenda->id} sebesar {$jumlahDenda} dibayar otomatis dari saldo user ID {$user->id_users}");
                            } else {
                                Log::warning("❌ Saldo user ID {$user->id_users} tidak cukup untuk membayar denda sebesar {$jumlahDenda}");
                            }
                        }
                    }
                    break;

                case 'pending':
                    $order->status = 'pending';
                    break;

                default:
                    $order->status = 'failed';
            }

            $order->transaction_details = json_encode($data);
            $order->save();
            Log::info("✅ Topup {$order->order_id} updated to {$order->status}");
        }


        // === 2. Cek dan update PARKIR jika ada ===
        $parkir = DataParkir::where('order_id', $data['order_id'])->first();
        if ($parkir) {
            if (in_array($data['transaction_status'], ['settlement', 'capture'])) {
                $parkir->status = 'Sudah Dibayar';
                $parkir->waktu_keluar = now();
                $parkir->save();

                Log::info("✅ Parkir {$parkir->kode_parkir} status updated to 'Sudah Dibayar'");

                // Kurangi saldo user
                $user = $parkir->user;
                if ($user && $user->saldo >= $parkir->total) {
                    $user->decrement('saldo', $parkir->total);
                    Log::info("💸 Saldo user ID {$user->id} dikurangi: {$parkir->total}");
                } else {
                    Log::warning("❌ Saldo tidak cukup atau user tidak ditemukan");
                }
            }
        }


        return response()->json(['message' => 'Notification handled'], 200);
    }
}
