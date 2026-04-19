<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiKerja;
use Inertia\Inertia;

class SesiKerjaController extends Controller
{
    public function index(Request $request)
    {
        $sesikerjas = SesiKerja::query()
            ->with(['leader']) // Mengambil data user yang jadi leader
            // Jika ada relasi ke master_departemen di model, tambahkan di sini
            // ->with(['departemen'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('leader', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                // Tambahkan pencarian berdasarkan jenis jika perlu
                ->orWhere('jenis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SesiKerjas/Index', [
            'sesikerjas' => $sesikerjas,
            'filters' => $request->only(['search'])
        ]);
    }
}
