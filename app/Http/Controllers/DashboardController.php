<?php

namespace App\Http\Controllers;

use App\Models\Cacat;
use App\Models\Produk;
use App\Models\Troli;
use App\Models\PengerjaanProduk;
use App\Models\PengerjaanCacat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Security Gate sesuai permintaanmu
        if (!$user->hasAnyRole(['admin', 'Manager Produksi'])) {
            return redirect()->route('trolis.index');
        }

        // 1. Ambil Stats Utama (Row Atas)
        $stats = [
            'total_produk' => Produk::count(),
            'proses_aktif' => PengerjaanProduk::whereNull('updated_at') // Asumsi jika belum update berarti sedang jalan
                                ->orWhereDate('created_at', today())
                                ->count(),
            'total_cacat_hari_ini' => PengerjaanCacat::whereDate('created_at', today())->count(),
            'troli_berjalan' => Troli::count(),
        ];

        // 2. Ambil Aktivitas Pengerjaan Terbaru
        $recentPengerjaan = PengerjaanProduk::with(['produk', 'proses', 'user'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'qrcode' => $item->produk->qrcode ?? 'N/A', // Sesuaikan kolom di table produk
                    'proses_nama' => $item->proses->proses,
                    'operator' => $item->user->name,
                    'waktu' => $item->created_at->diffForHumans(),
                ];
            });

        // 3. Data Top Cacat (Untuk Analisis)
        $topCacats = PengerjaanCacat::with('cacat')
            ->select('cacat_id', DB::raw('count(*) as total'))
            ->groupBy('cacat_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'statsData' => $stats,
            'recentActivities' => $recentPengerjaan,
            'topCacats' => $topCacats
        ]);
    }
}
