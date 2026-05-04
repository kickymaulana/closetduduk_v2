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
}
