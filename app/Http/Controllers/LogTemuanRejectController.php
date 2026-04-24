<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengerjaanCacat;
use Inertia\Inertia;

class LogTemuanRejectController extends Controller
{
    public function index(Request $request)
    {
        $logs = PengerjaanCacat::query()
            ->with([
                'cacat',
                'pengerjaan_produk.produk',
                'user_scan', // Pastikan relasi ini ada di model PengerjaanCacat
                'user_pj', // Pastikan relasi ini ada di model PengerjaanCacat
                'proses_pj', // Pastikan relasi ini ada di model PengerjaanCacat
                'proses_scan' // Pastikan relasi ini ada di model PengerjaanCacat
            ])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('cacat', function ($q) use ($search) {
                    $q->where('cacat', 'like', "%{$search}%");
                })->orWhereHas('pengerjaan_produk.produk', function ($q) use ($search) {
                    $q->where('qrcode', 'like', "%{$search}%"); // Sesuaikan kolom nama produk
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('LogTemuanReject/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search'])
        ]);
    }
}
