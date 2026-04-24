<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PengerjaanProduk;



class TotalPengerjaanUserController extends Controller
{
    public function index(Request $request)
    {
        $rekap = PengerjaanProduk::query()
            ->with(['user']) // Kita butuh data user untuk menampilkan nama
            ->select('user_id', \DB::raw('count(*) as total_pengerjaan'))
            // Pencarian berdasarkan nama user
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->groupBy('user_id')
            ->orderBy('total_pengerjaan', 'desc') // Urutkan dari yang paling rajin
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($item) => [
                'user' => [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                ],
                'total_pengerjaan' => $item->total_pengerjaan,
            ]);

        return Inertia::render('TotalPengerjaan/Index', [
            'rekap' => $rekap,
            'filters' => $request->only(['search']),
        ]);
    }
}
