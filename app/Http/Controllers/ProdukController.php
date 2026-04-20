<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\Produk;
use Inertia\Inertia;

class ProdukController extends Controller
{
    public function index(Request $request, Troli $troli)
    {
        // Kita ambil produk yang hanya milik troli ini
        $produks = Produk::query()
            ->where('troli_id', $troli->id)
            ->when($request->search, function ($query, $search) {
                $query->where('qrcode', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Trolis/Produk/Index', [
            'troli'   => $troli, // Kirim data detail troli-nya juga
            'produks' => $produks,
            'filters' => $request->only(['search'])
        ]);
    }
}
