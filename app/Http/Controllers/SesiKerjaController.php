<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiKerja;
use Inertia\Inertia;
use App\Models\User;
use App\Models\MasterDepartemen;
use Illuminate\Support\Facades\Auth;

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



    public function create()
    {
        return Inertia::render('SesiKerjas/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jam_masuk' => 'required|date',
            'jam_pulang' => 'nullable|date|after:jam_masuk',
            'jenis' => 'required|in:Body,Tangki',
        ]);

        // Tambahkan leader_id dari user yang sedang login
        $validated['leader_id'] = Auth::id();

        // Hardcode atau set null departemen_id jika di database masih ada kolomnya tapi belum dipakai
        $validated['departemen_id'] = 3; // Atau pastikan kolomnya nullable di migration

        SesiKerja::create($validated);

        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja berhasil dicatat.');
    }
}
