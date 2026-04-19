<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MasterCacat;

class CacatController extends Controller
{
    public function index(Request $request)
    {
        $cacats = MasterCacat::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nama_cacat', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Cacats/Index', [
            'cacats' => $cacats,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Cacats/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cacat' => 'required|string|max:255|unique:master_cacat,nama_cacat',
        ], [
            'nama_cacat.required' => 'Nama cacat wajib diisi.',
            'nama_cacat.unique' => 'Jenis cacat ini sudah terdaftar.',
        ]);

        MasterCacat::create($request->only('nama_cacat'));

        return redirect()->route('cacats.index')->with('message', 'Data cacat berhasil ditambahkan.');
    }

    public function edit(MasterCacat $cacat)
    {
        return Inertia::render('Master/Cacats/Edit', [
            'cacat' => $cacat
        ]);
    }

    public function update(Request $request, MasterCacat $cacat)
    {
        $request->validate([
            'nama_cacat' => 'required|string|max:255|unique:master_cacat,nama_cacat,' . $cacat->id,
        ]);

        $cacat->update($request->only('nama_cacat'));

        return redirect()->route('cacats.index')->with('message', 'Data cacat berhasil diperbarui.');
    }

    public function destroy(MasterCacat $cacat)
    {
        $cacat->delete();
        return redirect()->route('cacats.index')->with('message', 'Data cacat berhasil dihapus.');
    }
}
