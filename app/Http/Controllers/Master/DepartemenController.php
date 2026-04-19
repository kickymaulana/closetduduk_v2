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
}
