<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use Inertia\Inertia;

class TroliController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $trolis = Troli::query()
            // 1. Filter: Hanya ambil troli yang prosesnya milik departemen user login
            ->whereHas('proses', function ($query) use ($user) {
                $query->where('departemen_id', $user->departemen_id);
            })
            // 2. Load relasi (Eager Loading)
            ->with(['proses'])
            ->withCount(['produks'])
            // 3. Fitur Pencarian
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
