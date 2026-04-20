<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TroliFisik;
use Inertia\Inertia;

class TroliFisikController extends Controller
{
    public function index(Request $request)
    {
        $troliFisiks = TroliFisik::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nomor', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/TroliFisiks/Index', [
            'troliFisiks' => $troliFisiks,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/TroliFisiks/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string|max:255|unique:troli_fisik,nomor',
            'status' => 'required|in:Tidak,Digunakan',
        ], [
            'nomor.required' => 'Nomor troli wajib diisi.',
            'nomor.unique' => 'Nomor troli ini sudah terdaftar.',
        ]);

        TroliFisik::create($request->only('nomor', 'status'));

        return redirect()->route('master.trolifisiks.index')->with('message', 'Data troli fisik berhasil ditambahkan.');
    }

    public function edit(TroliFisik $trolifisik)
    {
        return Inertia::render('Master/TroliFisiks/Edit', [
            'troliFisik' => $trolifisik
        ]);
    }

    public function update(Request $request, TroliFisik $trolifisik)
    {
        $request->validate([
            // Unique kecuali untuk ID troli ini sendiri
            'nomor' => 'required|string|max:255|unique:troli_fisik,nomor,' . $trolifisik->id,
            'status' => 'required|in:Tidak,Digunakan',
        ], [
            'nomor.required' => 'Nomor troli wajib diisi.',
            'nomor.unique' => 'Nomor troli ini sudah digunakan.',
        ]);

        $trolifisik->update([
            'nomor' => $request->nomor,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('master.trolifisiks.index')
            ->with('message', 'Data troli fisik berhasil diperbarui.');
    }
    public function destroy(TroliFisik $trolifisik)
    {
        $trolifisik->delete();

        return redirect()
            ->route('master.trolifisiks.index')
            ->with('message', 'Data troli fisik berhasil dihapus.');
    }
}
