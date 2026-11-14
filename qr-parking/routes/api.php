<?php

use App\Http\Controllers\api\DendaControllerApi;
use App\Http\Controllers\Api\TopupController as ApiTopupController;
use App\Http\Controllers\apiTopupController as ControllersApiTopupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRuleController;
use App\Http\Controllers\catatWaktuParkirController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\scanController;
use App\Http\Controllers\staffController;
use App\Http\Controllers\tambahController;
use App\Http\Controllers\TopupController;
use App\Http\Middleware\CheckAdmin;
use App\Models\LeaveRequest;
use Dedoc\Scramble\Infer\Scope\Index;

// Public routes
// ne tugas bapak
Route::post('/tambahUser', [tambahController::class, 'tambahUser']);
Route::post('/userLogin', [tambahController::class, 'login']);
Route::get('/listUser', [tambahController::class, 'show']);
Route::get('/listUser2', [tambahController::class, 'showPaginate']);
Route::get('/showId/{id}', [tambahController::class, 'showId']);
Route::put('/updateUser/{id}', [tambahController::class, 'updateUser']);
Route::delete('/hapusUser/{id}', [tambahController::class, 'destroy']);




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/midtrans-notification', [TopupController::class, 'handleNotification']);

Route::get('/dendaIndex', [DendaControllerApi::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/update-user/{id_users}', [profileController::class, 'updateUserParking']);
    Route::post('/topup-store', [TopupController::class, 'store']);     // POST /api/topups
    Route::get('topup-show', [TopupController::class, 'showAktif']);    // GET /api/topups/{id}
    Route::get('user-show/{id}', [profileController::class, 'showIdUser']);    // GET /api/topups/{id}
    Route::middleware('auth:sanctum')->delete('/topup/pending/delete', [TopupController::class, 'deletePendingTopupForUser']);

    // routes/api.php

    Route::get('topup-show-saldo/{user_id}', [TopupController::class, 'show']);    // GET /api/topups/{id}
    Route::get('topup-show-saldo-all/{user_id}', [TopupController::class, 'showAll']);    // GET /api/topups/{id}
    Route::get('topup-show-status/{user_id}', [TopupController::class, 'CheckStatus']);    // GET /api/topups/{id}
    Route::post('/kendaraan', [KendaraanController::class, 'store']);
    Route::get('/kendaraan-index', [KendaraanController::class, 'index']);
    Route::get('/kendaraan-show/{user_id}', [KendaraanController::class, 'show']);
    Route::delete('/kendaraan-destroy/{id}', [KendaraanController::class, 'destroy']);
    Route::put('/kendaraan-aktifkan/{id}', [KendaraanController::class, 'updateIsAktif']);

    Route::post('/dataParkir/{user_id}', [scanController::class, 'statusCheck']);
    Route::post('/validasi-qr', [scanController::class, 'validasi']);
    Route::get('/data-parkir/plat/{plat}', [catatWaktuParkirController::class, 'showByPlat']);



    Route::post('/catat-waktu-masuk', [catatWaktuParkirController::class, 'storeWaktuMasuk']);
    Route::get('/catat-waktu-masuk/{id}', [catatWaktuParkirController::class, 'showTarif']);
    Route::get('/tampil-property', [catatWaktuParkirController::class, 'show']);
    Route::get('/tampil-riwayat-kendaraan/{user_id}', [catatWaktuParkirController::class, 'showHistory']);

    Route::get('/tampil-riwayat-kendaraan-all/{user_id}', [catatWaktuParkirController::class, 'showHistoryAll']);

    Route::get('/parkir-masuk', [catatWaktuParkirController::class, 'showParkirMasuk']);
    Route::get('/cek-status-parkir/{kode_parkir}', [catatWaktuParkirController::class, 'cekStatusParkir']);
    Route::put('/parkir/{kode_parkir}', [catatWaktuParkirController::class, 'updateParkirByKode']);

    Route::get('/tampil-property/parkir', [catatWaktuParkirController::class, 'generateKodeParkir']);
    Route::get('/denda/user/{id_users}', [profileController::class, 'showDendaPendingByUserId']);
    Route::put('/denda/bayar', [profileController::class, 'bayarDenda']);





    Route::middleware(['auth:sanctum', 'staff'])->group(function () {
        Route::get('/data-parkir/plat/{plat}', [staffController::class, 'showByPlat']);
        Route::post('/kendaraan/store', [staffController::class, 'storeDenda']);
        Route::post('/kendaraan/dendaNonUser', [staffController::class, 'storeDendaNonUser']);
        Route::put('/update-staff/{id_users}', [staffController::class, 'updateUserParking']);
        Route::get('/kendaraan/plat/{plat_nomor}', [staffController::class, 'showKendaraanByPlatNomor']);
    });
});
