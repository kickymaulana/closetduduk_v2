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

    Route::get('master/cacats', [CacatController::class, 'index'])->name('cacats.index');
    Route::get('master/cacats/create', [CacatController::class, 'create'])->name('cacats.create');
    Route::post('master/cacats/create', [CacatController::class, 'store'])->name('cacats.store');
    Route::get('master/cacats/{cacat}/edit', [CacatController::class, 'edit'])->name('cacats.edit');
    Route::put('master/cacats/{cacat}/edit', [CacatController::class, 'update'])->name('cacats.update');
    Route::delete('master/cacats/{cacat}', [CacatController::class, 'destroy'])->name('cacats.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
