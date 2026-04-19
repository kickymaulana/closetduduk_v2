<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Arahkan Manager Tingkat Atas ke Persetujuan Manager
        if ($user->hasAnyRole(['QC Manager', 'Factory Manager', 'General Manager'])) {
            return redirect()->route('persetujuan.manager.index');
        }

        // 2. Arahkan Manager, Supervisor, Leader, dan Operator ke Tugas Produksi
        if ($user->hasAnyRole(['Manager', 'Supervisor', 'Leader', 'Operator'])) {
            return redirect()->route('tugas.produksi.index');
        }

        // 3. Jika login sebagai Admin atau Quality Control, tampilkan Dashboard utama
        return Inertia::render('Dashboard/Index');
    }

    public function testing()
    {
        return Inertia::render('Dashboard/Testing');
    }
}
