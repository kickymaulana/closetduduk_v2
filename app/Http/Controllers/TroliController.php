<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use Inertia\Inertia;

class TroliController extends Controller
{
    public function index(Request $request)
    {
        $trolis = Troli::query()
            ->with(['proses']) // Memuat relasi proses
            ->when($request->search, function ($query, $search) {
                $query->where('invoice', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Trolis/Index', [
            'trolis' => $trolis,
            'filters' => $request->only(['search'])
        ]);
    }
}
