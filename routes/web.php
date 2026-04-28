<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Master\CacatController;
use App\Http\Controllers\Master\AturanPenolakanController;
use App\Http\Controllers\SesiKerjaController;
use App\Http\Controllers\Master\TroliFisikController as MasterTroliFisikController;
use App\Http\Controllers\Master\ProsesController;
use App\Http\Controllers\Master\ShiftController;
use App\Http\Controllers\TroliController;
use App\Http\Controllers\TroliFisikController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\RiwayatScanMasukController;
use App\Http\Controllers\TotalPengerjaanUserController;
use App\Http\Controllers\LogTemuanRejectController;
use App\Http\Controllers\ProsesProduksiController;
use App\Http\Controllers\StokController;



Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('master/users', [UserController::class, 'index'])->name('users.index');
    Route::get('master/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('master/users/create', [UserController::class, 'store'])->name('users.store');
    Route::get('master/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('master/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('master/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('master/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('master/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::get('master/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
    Route::post('master/shifts/create', [ShiftController::class, 'store'])->name('shifts.store');
    Route::get('master/shifts/{shift}/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
    Route::put('master/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('master/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    Route::get('master/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('master/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('master/roles/create', [RoleController::class, 'store'])->name('roles.store');
    Route::get('master/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('master/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('master/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('master/departemens', [DepartemenController::class, 'index'])->name('departemens.index');
    Route::get('master/departemens/create', [DepartemenController::class, 'create'])->name('departemens.create');
    Route::post('master/departemens/create', [DepartemenController::class, 'store'])->name('departemens.store');
    Route::get('master/departemens/{departemen}/edit', [DepartemenController::class, 'edit'])->name('departemens.edit');
    Route::put('master/departemens/{departemen}/edit', [DepartemenController::class, 'update'])->name('departemens.update');
    Route::delete('master/departemens/{departemen}', [DepartemenController::class, 'destroy'])->name('departemens.destroy');

    Route::get('master/proses', [ProsesController::class, 'index'])->name('proses.index');
    Route::get('master/proses/create', [ProsesController::class, 'create'])->name('proses.create');
    Route::post('master/proses/create', [ProsesController::class, 'store'])->name('proses.store');
    Route::get('master/proses/{proses}/edit', [ProsesController::class, 'edit'])->name('proses.edit');
    Route::put('master/proses/{proses}/edit', [ProsesController::class, 'update'])->name('proses.update');
    Route::delete('master/proses/{proses}', [ProsesController::class, 'destroy'])->name('proses.destroy');

    Route::get('master/cacats', [CacatController::class, 'index'])->name('cacats.index');
    Route::get('master/cacats/create', [CacatController::class, 'create'])->name('cacats.create');
    Route::post('master/cacats/create', [CacatController::class, 'store'])->name('cacats.store');
    Route::get('master/cacats/{cacat}/edit', [CacatController::class, 'edit'])->name('cacats.edit');
    Route::put('master/cacats/{cacat}/edit', [CacatController::class, 'update'])->name('cacats.update');
    Route::delete('master/cacats/{cacat}', [CacatController::class, 'destroy'])->name('cacats.destroy');

    Route::get('master/aturan-penolakans', [AturanPenolakanController::class, 'index'])->name('aturanpenolakans.index');
    Route::get('master/aturan-penolakans/create', [AturanPenolakanController::class, 'create'])->name('aturanpenolakans.create');
    Route::post('master/aturan-penolakans/create', [AturanPenolakanController::class, 'store'])->name('aturanpenolakans.store');
    Route::get('master/aturan-penolakans/{cacat}/edit', [AturanPenolakanController::class, 'edit'])->name('aturanpenolakans.edit');
    Route::put('master/aturan-penolakans/{cacat}/edit', [AturanPenolakanController::class, 'update'])->name('aturanpenolakans.update');
    Route::delete('master/aturan-penolakans/{cacat}', [AturanPenolakanController::class, 'destroy'])->name('aturanpenolakans.destroy');

    Route::get('master/troli-fisiks', [MasterTroliFisikController::class, 'index'])->name('master.trolifisiks.index');
    Route::get('master/troli-fisiks/create', [MasterTroliFisikController::class, 'create'])->name('master.trolifisiks.create');
    Route::post('master/troli-fisiks/create', [MasterTroliFisikController::class, 'store'])->name('master.trolifisiks.store');
    Route::get('master/troli-fisiks/{trolifisik}/edit', [MasterTroliFisikController::class, 'edit'])->name('master.trolifisiks.edit');
    Route::put('master/troli-fisiks/{trolifisik}/edit', [MasterTroliFisikController::class, 'update'])->name('master.trolifisiks.update');
    Route::delete('master/troli-fisiks/{trolifisik}', [MasterTroliFisikController::class, 'destroy'])->name('master.trolifisiks.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('sesi-kerjas', [SesiKerjaController::class, 'index'])->name('sesikerjas.index');
    Route::get('sesi-kerjas/create', [SesiKerjaController::class, 'create'])->name('sesikerjas.create');
    Route::post('sesi-kerjas/create', [SesiKerjaController::class, 'store'])->name('sesikerjas.store');
    Route::get('sesi-kerjas/{sesikerja}', [SesiKerjaController::class, 'show'])->name('sesikerjas.show');
    Route::get('sesi-kerjas/{sesikerja}/riwayat-scan', [SesiKerjaController::class, 'riwayat_scan'])->name('sesikerjas.riwayat_scan');
    Route::get('sesi-kerjas/{sesikerja}/edit', [SesiKerjaController::class, 'edit'])->name('sesikerjas.edit');
    Route::put('sesi-kerjas/{sesikerja}/edit', [SesiKerjaController::class, 'update'])->name('sesikerjas.update');
    Route::post('sesi-kerjas/{sesikerja}/aktifkan', [SesiKerjaController::class, 'aktifkan'])->name('sesikerjas.aktifkan');
    Route::delete('sesi-kerjas/{sesikerja}/nonaktif', [SesiKerjaController::class, 'nonaktif'])->name('sesikerjas.nonaktif');
    Route::delete('sesi-kerjas/{sesikerja}', [SesiKerjaController::class, 'destroy'])->name('sesikerjas.destroy');

    Route::get('trolis', [TroliController::class, 'index'])->name('trolis.index');
    Route::get('trolis/ambil', [TroliController::class, 'ambil'])->name('trolis.ambil');
    Route::post('trolis/ambilproses', [TroliController::class, 'ambilproses'])->name('trolis.ambilproses');
    Route::get('trolis/trolikosong', [TroliController::class, 'trolikosong'])->name('trolis.trolikosong');
    Route::post('trolis/trolikosong', [TroliController::class, 'trolikosong_store'])->name('trolis.trolikosong.store');
    Route::post('trolis/{troli}/selesaikan', [TroliController::class, 'selesaikan_troli'])->name('trolis.selesaikan');
    Route::get('trolis/{troli}/kembalikan', [TroliController::class, 'kembalikan'])->name('trolis.kembalikan');
    Route::post('trolis/{troli}/kembalikan', [TroliController::class, 'kembalikan_store'])->name('trolis.kembalikan_store');
    Route::post('trolis/{troli}/hapus', [TroliController::class, 'hapus_store'])->name('trolis.hapus_store');
    Route::get('trolis/{troli}/produk', [ProdukController::class, 'index'])->name('trolis.produk.index');
    Route::get('trolis/{troli}/produk/scan-awal', [ProdukController::class, 'scan_awal'])->name('trolis.produk.scan_awal');
    Route::post('trolis/{troli}/produk/scan-awal', [ProdukController::class, 'scan_awal_store'])->name('trolis.produk.scan_awal_store');
    Route::get('trolis/{troli}/produk/scan', [ProdukController::class, 'scan'])->name('trolis.produk.scan');
    Route::post('trolis/{troli}/produk/scan', [ProdukController::class, 'scan_store'])->name('trolis.produk.scan_store');
    Route::get('trolis/{troli}/produk/scan-inproses', [ProdukController::class, 'scan_inproses'])->name('trolis.produk.scan_inproses');
    Route::post('trolis/{troli}/produk/scan-inproses', [ProdukController::class, 'scan_inproses_store'])->name('trolis.produk.scan_inproses_store');
    Route::get('trolis/{troli}/produk/scan-buang', [ProdukController::class, 'scan_buang'])->name('trolis.produk.scan_buang');
    Route::post('trolis/{troli}/produk/scan-buang', [ProdukController::class, 'scan_buang_store'])->name('trolis.produk.scan_buang_store');
    Route::get('trolis/{troli}/produk/scan-pindah', [ProdukController::class, 'scan_pindah'])->name('trolis.produk.scan_pindah');
    Route::post('trolis/{troli}/produk/scan-pindah', [ProdukController::class, 'scan_pindah_store'])->name('trolis.produk.scan_pindah_store');
    Route::get('trolis/{troli}/produk/scan-hapus', [ProdukController::class, 'scan_hapus'])->name('trolis.produk.scan_hapus');
    Route::post('trolis/{troli}/produk/scan-hapus', [ProdukController::class, 'scan_hapus_store'])->name('trolis.produk.scan_hapus_store');

    Route::get('troli-fisiks', [TroliFisikController::class, 'index'])->name('trolifisiks.index');
    Route::post('troli-fisiks/ambil', [TroliFisikController::class, 'ambil'])->name('trolifisiks.ambil');

    Route::get('riwayat-scan-masuk', [RiwayatScanMasukController::class, 'index'])->name('riwayat.scan.masuk');
    Route::get('total-pengerjaan-user', [TotalPengerjaanUserController::class, 'index'])->name('total.pengerjaan.user');
    Route::get('log-temuan-reject', [LogTemuanRejectController::class, 'index'])->name('log.temuan.reject');
    Route::get('trolis', [TroliController::class, 'index'])->name('trolis.index');

    Route::get('produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
    Route::get('produk', [ProdukController::class, 'dataprodukindex'])->name('produk.index');

    Route::get('proses-produksi', [ProsesProduksiController::class, 'index'])->name('proses.produksi');
    Route::get('stok', [StokController::class, 'index'])->name('stok');
});
