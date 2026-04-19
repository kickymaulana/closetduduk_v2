<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDepartemen;
use Inertia\Inertia;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $departemens = MasterDepartemen::query()
            ->when($request->search, function ($query, $search) {
                $query->where('departemen', 'like', "%{$search}%");
            })
            ->orderBy('urutan', 'asc') // Urutkan berdasarkan kolom urutan
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Departemens/Index', [
            'departemens' => $departemens,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Departemens/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'departemen' => 'required|string|max:255|unique:master_departemen,departemen',
            'urutan' => 'required|integer|min:0',
        ], [
            'departemen.unique' => 'Nama departemen sudah ada.',
            'departemen.required' => 'Nama departemen wajib diisi.',
            'urutan.required' => 'Nomor urutan wajib diisi.',
        ]);

        MasterDepartemen::create([
            'departemen' => $request->departemen,
            'urutan' => $request->urutan,
        ]);

        return redirect()->route('departemens.index')
            ->with('message', 'Departemen berhasil ditambahkan!');
    }
}
