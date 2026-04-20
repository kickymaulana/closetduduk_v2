<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\Proses;
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


    public function selesaikan_troli(Troli $troli)
    {
        $urutanSekarang = $troli->proses->urutan;

        $prosesBerikutnya = \App\Models\Proses::where('departemen_id', $troli->proses->departemen_id)
            ->where('urutan', '>', $urutanSekarang)
            ->orderBy('urutan', 'asc')
            ->first();

        if ($prosesBerikutnya) {
            // 1. Update status troli dan pindah proses
            $troli->update([
                'status' => 'Selesai',
                'proses_id' => $prosesBerikutnya->id
            ]);

            // 2. Reset status "sudah_scan" semua produk di dalam troli ini
            $troli->produks()->update([
                'sudah_scan' => 'Belum'
            ]);

            return back()->with('message', 'Troli dilanjutkan ke: ' . $prosesBerikutnya->proses);
        }

        return back()->with('error', 'Tidak ada proses lanjutan ditemukan.');
    }
}
