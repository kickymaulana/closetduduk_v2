<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Warna;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarnaController extends Controller
{
    public function index(Request $request)
    {
        $warnas = Warna::query()
            ->when($request->search, function ($query, $search) {
                $query->where('warna', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Warna/Index', [
            'warnas' => $warnas,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Warna/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'warna' => 'required|string|max:255|unique:warna,warna',
        ], [
            'warna.required' => 'Nama warna wajib diisi.',
            'warna.unique' => 'Warna ini sudah terdaftar.',
        ]);

        Warna::create($request->only('warna'));

        return redirect()->route('warna.index')->with('message', 'Data warna berhasil ditambahkan.');
    }

    public function edit(Warna $warna)
    {
        return Inertia::render('Master/Warna/Edit', [
            'warna' => $warna
        ]);
    }

    public function update(Request $request, Warna $warna)
    {
        $request->validate([
            'warna' => 'required|string|max:255|unique:warna,warna,' . $warna->id,
        ]);

        $warna->update($request->only('warna'));

        return redirect()->route('warna.index')->with('message', 'Data warna berhasil diperbarui.');
    }

    public function destroy(Warna $warna)
    {
        $warna->delete();
        return redirect()->route('warna.index')->with('message', 'Data warna berhasil dihapus.');
    }
}
