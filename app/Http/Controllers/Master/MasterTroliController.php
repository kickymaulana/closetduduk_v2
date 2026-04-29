<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Troli;
use App\Models\Produk;

class MasterTroliController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $user = auth()->user();

        $query = Troli::with(['proses'])
            ->whereHas('proses', function ($query) use ($user) {
                $query->where('departemen_id', $user->departemen_id);
            })
            ->withCount('produks');

        if ($search) {
            $query->where(function ($q) use ($search) {
                // 1. Cari berdasarkan nomor invoice troli
                $q->where('nomor', 'like', "%{$search}%")
                // 2. ATAU cari berdasarkan qrcode produk
                ->orWhereHas('produks', function ($pq) use ($search) {
                    $pq->where('qrcode', 'like', "%{$search}%");
                });
            });
        }

        $trolis = $query->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($troli) => [
                'id' => $troli->id,
                'nomor' => $troli->nomor,
                'keperluan' => $troli->keperluan,
                'status' => $troli->status,
                'proses' => $troli->proses,
                'produks_count' => $troli->produks_count,
                // Penyesuaian untuk tampilan di Vue kamu
                'terakhir_diperbaharui_jam' => $troli->updated_at->format('H:i'),
                'terakhir_diperbaharui' => $troli->updated_at->format('d M Y'),
            ]);

        return Inertia::render('Master/Trolis/Index', [
            'trolis' => $trolis,
            'filters' => $request->only(['search']),
        ]);
    }


    public function produk(Request $request, Troli $troli)
    {
        $troli->load('proses');

        $produks = Produk::query()
            ->where('troli_id', $troli->id)
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('qrcode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Ambil daftar troli lain untuk keperluan "Pindahkan ke Troli Lain"
        $availableTrolis = Troli::where('id', '!=', $troli->id)
            ->whereNotNull('proses_id')
            ->select('id', 'nomor')
            ->get();

        return Inertia::render('Master/Trolis/Produk', [
            'troli' => $troli,
            'produks' => $produks,
            'availableTrolis' => $availableTrolis,
            'filters' => $request->only(['search']),
        ]);
    }
}
