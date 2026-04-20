<?php

namespace App\Http\Controllers;

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

        return Inertia::render('TroliFisiks/Index', [
            'troliFisiks' => $troliFisiks,
            'filters' => $request->only(['search'])
        ]);
    }
}
