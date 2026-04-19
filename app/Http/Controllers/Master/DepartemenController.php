<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departemen;
use Inertia\Inertia;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $departemens = Departemen::query()
            ->when($request->search, function ($query, $search) {
                $query->where('departemen', 'like', "%{$search}%");
            })
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
            'departemen' => 'required|string|max:255|unique:departemen,departemen',
        ], [
            'departemen.unique' => 'Nama departemen sudah ada.',
            'departemen.required' => 'Nama departemen wajib diisi.',
        ]);

        Departemen::create([
            'departemen' => $request->departemen,
        ]);

        return redirect()->route('departemens.index')
            ->with('message', 'Departemen berhasil ditambahkan!');
    }


    public function edit(Departemen $departemen)
    {
        return Inertia::render('Master/Departemens/Edit', [
            'departemen' => $departemen
        ]);
    }

    public function update(Request $request, Departemen $departemen)
    {
        $request->validate([
            // unique:table,column,except_id
            'departemen' => 'required|string|max:255|unique:departemen,departemen,' . $departemen->id,
        ], [
            'departemen.unique' => 'Nama departemen sudah ada.',
            'departemen.required' => 'Nama departemen wajib diisi.',
        ]);

        $departemen->update([
            'departemen' => $request->departemen,
        ]);

        return redirect()->route('departemens.index')
            ->with('message', 'Data departemen berhasil diperbarui!');
    }

    public function destroy(Departemen $departemen)
    {
        // Opsional: Tambahkan pengecekan jika departemen masih digunakan di tabel lain
        // if ($departemen->units()->exists()) {
        //     return back()->with('error', 'Departemen tidak bisa dihapus karena masih memiliki data unit.');
        // }

        $departemen->delete();

        return redirect()->route('departemens.index')
            ->with('message', 'Departemen berhasil dihapus selamanya.');
    }
}
