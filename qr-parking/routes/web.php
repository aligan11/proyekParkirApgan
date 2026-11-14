<?php

use App\Http\Controllers\ControllersWeb\AdminController;
use App\Http\Controllers\ControllersWeb\AuthController;
use App\Http\Controllers\ControllersWeb\DashboardController;
use App\Http\Controllers\ControllersWeb\dataKendaraanController;
use App\Http\Controllers\ControllersWeb\dataParkirController;
use App\Http\Controllers\ControllersWeb\homeController;
use App\Http\Controllers\ControllersWeb\ParkirExportController;
use App\Http\Controllers\ControllersWeb\topUpController;
use App\Http\Middleware\MiddlewareWeb\loggedIn;
use App\Http\Middleware\MiddlewareWeb\loginCheck;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\RoleMiddleware;
use App\Models\DataKendaraan;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/register', [homeController::class, 'daftar'])->name('daftar');
Route::post('/register', [homeController::class, 'prosesDaftar'])->name('prosesDaftar');
Route::get('monitoring', [homeController::class, 'monitoring'])->name('monitoring');
// routes/web.php

// Home dengan form login
route::middleware(loginCheck::class)->group(function () {});
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::post('/login', [AuthController::class, 'loginProcess'])->name('loginAdmin');


/**
 * Dashboard & seluruh fitur operasional.
 * Dapat diakses oleh role: admin_super dan admin.
 */
Route::middleware(['check.role:admin_super,admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('dataparkir', dataParkirController::class);

    Route::get('/parkir-denda', [DashboardController::class, 'indexDenda'])->name('parkir.denda');
    Route::put('/denda/{id}/selesai', [DashboardController::class, 'dendaSelesai'])
        ->name('denda.selesai');
    Route::get('/parkir-income', [DashboardController::class, 'incomeAdmin'])->name('parkir.income');
    Route::get('/data-parkir-masuk', [DashboardController::class, 'tampilDataParkir'])->name('data.parkir.masuk');
    Route::get('/data-parkir-keluar', [DashboardController::class, 'tampilDataParkirKeluar'])->name('data.parkir.keluar');
    Route::get('/data-parkir-gabungan', [DashboardController::class, 'tampilDataParkirGabungan'])->name('data.parkir.gabungan');
    Route::post('/admin/parkir/export', [ParkirExportController::class, 'export'])->name('parkir.export');
    Route::post('/export-parkir', [ParkirExportController::class, 'export'])->name('export.parkir');

    Route::get('/data-aplikasi', [DashboardController::class, 'tampilDataAplikasi'])->name('data.aplikasi');
    Route::post('/aplikasi/update', [DashboardController::class, 'update'])->name('aplikasi.update');
    Route::get('/aplikasi/reset', [DashboardController::class, 'reset'])->name('aplikasi.reset');

    Route::get('/data-jenis-kendaraan', [dataKendaraanController::class, 'tampilDataKendaraan'])->name('data.kendaraan');
    Route::post('/data-kendaraan/store', [dataKendaraanController::class, 'store'])->name('data.kendaraan.store');
    Route::put('/update-jenis-kendaraan/{id}', [dataKendaraanController::class, 'update'])->name('update');
    Route::delete('/data-kendaraan/delete/{id}', [dataKendaraanController::class, 'destroy'])->name('destroy');

    Route::get('/data-latar-belakang', [DashboardController::class, 'tampilDataBg'])->name('data.bg');

    Route::resource('topup-parkir', topUpController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * Manajemen Pengguna (Data Admin, Data User, Data Staff).
     * HANYA dapat diakses role: admin_super.
     * Role admin tidak boleh melihat menu maupun mengakses route ini.
     */
    Route::middleware(['check.role:admin_super'])->group(function () {
        Route::get('/create-admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/store-admin', [AdminController::class, 'store'])->name('admin.store');
        Route::put('/update/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/destroy/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

        Route::get('/createUser', [AdminController::class, 'createUser'])->name('admin.createUser');
        Route::post('/createUser', [AdminController::class, 'storeUser'])->name('admin.storeUser');
        Route::put('/edit-user/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');

        Route::get('/createStaff', [AdminController::class, 'createStaff'])->name('admin.createStaff');
        Route::post('/storeStaff', [AdminController::class, 'storeStaff'])->name('admin.storeStaff');
        Route::delete('/hapus-staff/{id}', [AdminController::class, 'destroyStaff'])->name('admin.destroyStaff');

        Route::get('/get-user/{id}', [AdminController::class, 'getUser'])->name('admin.getUser');
    });
});


Route::middleware(['auth', 'role:user'])->group(function () {});
