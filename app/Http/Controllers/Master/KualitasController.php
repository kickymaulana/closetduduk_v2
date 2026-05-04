<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kualitas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KualitasController extends Controller
{
    public function index(Request $request)
    {
        $kualitas = Kualitas::query()
            ->when($request->search, function ($query, $search) {
                $query->where('kualitas', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Kualitas/Index', [
            'kualitas' => $kualitas,
            'filters' => $request->only(['search'])
        ]);
    }

    // Kamu bisa menambahkan method create, store, dll nanti sesuai kebutuhan
    // dengan pola yang sama seperti CacatController.

    public function create()
    {
        return Inertia::render('Master/Kualitas/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kualitas' => 'required|string|max:255|unique:kualitas,kualitas',
        ], [
            'kualitas.required' => 'Nama kualitas wajib diisi.',
            'kualitas.unique' => 'Nama kualitas ini sudah terdaftar.',
        ]);

        \App\Models\Kualitas::create($request->only('kualitas'));

        return redirect()->route('kualitas.index')->with('message', 'Data kualitas berhasil ditambahkan.');
    }

    public function edit(Kualitas $kualitas)
    {
        return Inertia::render('Master/Kualitas/Edit', [
            'kualitas' => $kualitas
        ]);
    }

    public function update(Request $request, Kualitas $kualitas)
    {
        $request->validate([
            'kualitas' => 'required|string|max:255|unique:kualitas,kualitas,' . $kualitas->id,
        ], [
            'kualitas.required' => 'Nama kualitas wajib diisi.',
            'kualitas.unique' => 'Nama kualitas ini sudah terdaftar.',
        ]);

        $kualitas->update($request->only('kualitas'));

        return redirect()->route('kualitas.index')->with('message', 'Data kualitas berhasil diperbarui.');
    }

    public function destroy(Kualitas $kualitas)
    {
        $kualitas->delete();
        return redirect()->route('kualitas.index')->with('message', 'Data kualitas berhasil dihapus.');
    }
}
