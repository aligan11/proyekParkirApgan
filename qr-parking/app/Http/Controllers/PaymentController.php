<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Request $request)
    {
        // Validasi input dari Flutter
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:1000', // Minimum amount for Midtrans
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            // Tambahkan validasi lain sesuai kebutuhan (misal: item_details)
        ]);

        $params = array(
            'transaction_details' => array(
                'order_id' => $request->order_id,
                'gross_amount' => $request->amount,
            ),
            'customer_details' => array(
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                // 'phone' => '081234567890', // Opsional
            ),
            // 'item_details' => [
            //     [
            //         'id' => 'ITEM001',
            //         'price' => $request->amount,
            //         'quantity' => 1,
            //         'name' => 'Top Up Balance'
            //     ]
            // ],
            'enabled_payments' => [
                'shopeepay',
                'gopay',
                'ovo',
                'danamon_online', // Contoh metode pembayaran yang diaktifkan
                'bca_va',
                'bri_va',
                'bni_va',
                'mandiri_va',
                'credit_card'
            ],
            // 'callbacks' => [
            //     'finish' => 'YOUR_APP_SUCCESS_URL',
            //     'error' => 'YOUR_APP_ERROR_URL',
            //     'pending' => 'YOUR_APP_PENDING_URL',
            // ],
        );

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json([
                'snap_token' => $snapToken,
                'transaction_details' => $params['transaction_details'] // Kirim detail transaksi kembali jika perlu
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Untuk handle notifikasi dari Midtrans (Webhook)
    public function handleNotification(Request $request)
    {
        $notification = new \Midtrans\Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;

        // Implementasi logika Anda di sini:
        // - Cari transaksi berdasarkan $orderId di database Anda.
        // - Update status transaksi di database berdasarkan $transactionStatus.
        // - Tangani status fraud.

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // TODO Set transaction status on your database to 'challenge'
            } else if ($fraudStatus == 'accept') {
                // TODO Set transaction status on your database to 'success'
            }
        } else if ($transactionStatus == 'settlement') {
            // TODO Set transaction status on your database to 'success'
        } else if ($transactionStatus == 'pending') {
            // TODO Set transaction status on your database to 'pending' / waiting payment
        } else if ($transactionStatus == 'deny') {
            // TODO Set transaction status on your database to 'deny'
        } else if ($transactionStatus == 'expire') {
            // TODO Set transaction status on your database to 'expire'
        } else if ($transactionStatus == 'cancel') {
            // TODO Set transaction status on your database to 'cancel'
        }

        return response('OK', 200);
    }
}
