<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiKerja;
use Inertia\Inertia;
use App\Models\User;
use App\Models\MasterDepartemen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SesiKerjaController extends Controller
{
    public function index(Request $request)
    {
        $sesikerjas = SesiKerja::query()
            ->with(['leader'])
            ->withCount('pengerjaan_produks')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('leader', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('jenis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SesiKerjas/Index', [
            'sesikerjas' => $sesikerjas,
            'filters' => $request->only(['search']),
            'sesi_kerja_id' => session('sesi_kerja_id'),
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
        $validated['leader_id'] = Auth::id();

        SesiKerja::create($validated);

        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja berhasil dicatat.');
    }


    public function show(SesiKerja $sesikerja)
    {
        $sesikerja->load(['leader']);

        return Inertia::render('SesiKerjas/Show', [
            'sesikerja' => $sesikerja
        ]);
    }

    public function edit(SesiKerja $sesikerja)
    {
        return Inertia::render('SesiKerjas/Edit', [
            'sesikerja' => $sesikerja
        ]);
    }

    public function update(Request $request, SesiKerja $sesikerja)
    {
        $validated = $request->validate([
            'jam_masuk' => 'required|date',
            'jam_pulang' => 'nullable|date|after:jam_masuk',
            'jenis' => 'required|in:Body,Tangki',
        ]);

        $sesikerja->update($validated);

        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja berhasil diperbarui.');
    }

    public function aktifkan(SesiKerja $sesikerja)
    {
        session(['sesi_kerja_id' => $sesikerja->id]);

        return Redirect::route('sesikerjas.index')
            ->with('message', "Sesi {$sesikerja->jenis} diaktifkan.");
    }

    public function nonaktif()
    {
        session()->forget('sesi_kerja_id');

        return Redirect::route('sesikerjas.index')
            ->with('message', 'Sesi kerja dinonaktifkan.');
    }
}
