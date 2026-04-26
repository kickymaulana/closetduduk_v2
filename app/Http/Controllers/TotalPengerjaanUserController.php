<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PengerjaanProduk;
use Illuminate\Support\Facades\DB;



class TotalPengerjaanUserController extends Controller
{
    public function index(Request $request)
{
    $rekap = PengerjaanProduk::query()
        ->with(['user'])
        ->select(
            'user_id',
            DB::raw('count(*) as total_pengerjaan'),
            // Hitung berdasarkan status_kondisi (sesuaikan dengan enum di DB kamu)
            DB::raw("COUNT(IF(status_kondisi = 'OK', 1, NULL)) as total_ok"),
            DB::raw("COUNT(IF(status_kondisi = 'In Proses', 1, NULL)) as total_proses"),
            DB::raw("COUNT(IF(status_kondisi = 'Buang', 1, NULL)) as total_buang")
        )

        // Filter Tanggal dan Jam
        ->when($request->date_start && $request->date_end, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->date_start, $request->date_end]);
        })

        ->when($request->search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->groupBy('user_id')
        ->orderBy('total_pengerjaan', 'desc')
        ->paginate(15)
        ->withQueryString()
        ->through(fn ($item) => [
            'user' => [
                'id' => $item->user->id,
                'name' => $item->user->name,
            ],
            'total_pengerjaan' => $item->total_pengerjaan,
            'total_ok' => $item->total_ok,
            'total_proses' => $item->total_proses,
            'total_buang' => $item->total_buang,
        ]);

    return Inertia::render('TotalPengerjaan/Index', [
        'rekap' => $rekap,
        'filters' => $request->only(['search', 'date_start', 'date_end']),
    ]);
}

}
