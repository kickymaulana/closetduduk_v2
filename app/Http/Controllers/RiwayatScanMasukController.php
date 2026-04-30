<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PengerjaanProduk;

class RiwayatScanMasukController extends Controller
{
    public function index(Request $request)
    {
        $riwayat = PengerjaanProduk::query()
            // Eager load relasi agar tidak N+1 query
            ->with(['produk', 'proses', 'user', 'sesiKerja.leader'])
            // Pencarian berdasarkan kode produk (invoice) atau nama operator
            ->when($request->search, function ($query, $search) {
                $query->whereHas('produk', function ($q) use ($search) {
                    $q->where('qrcode', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString()
            // Kita gunakan through untuk memastikan data terformat dengan baik
            ->through(fn ($item) => [
                // Mengambil ID dari table pengerjaan (opsional, ganti nama key jika perlu)
                'id_pengerjaan' => $item->id,

                // Mengambil ID dari table produk
                'id' => $item->produk->id ?? null,
                'produk' => $item->produk,
                'proses' => $item->proses,
                'user' => $item->user,
                'status_kondisi' => $item->status_kondisi,
                // Menggunakan Accessor yang kita buat di model PengerjaanProduk nanti
                'waktu_scan' => $item->created_at->translatedFormat('d M Y, H:i'),
                'waktu_relatif' => $item->created_at->diffForHumans(),
            ]);

        return Inertia::render('RiwayatScanMasuk/Index', [
            'riwayat' => $riwayat,
            'filters' => $request->only(['search']),
        ]);
    }
}
