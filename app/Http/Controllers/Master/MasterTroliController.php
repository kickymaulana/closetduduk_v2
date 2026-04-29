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
        $user = auth()->user(); // Ambil data user login

        $produks = Produk::query()
            ->where('troli_id', $troli->id)
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('qrcode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Filter troli: Departemen sama, bukan troli saat ini, dan sudah ada prosesnya
        $availableTrolis = Troli::where('id', '!=', $troli->id)
            ->whereHas('proses', function ($q) use ($user) {
                $q->where('departemen_id', $user->departemen_id);
            })
            ->select('id', 'nomor')
            ->orderBy('nomor', 'asc') // Urutkan agar mudah dicari
            ->get();

        $allProses = \App\Models\Proses::select('id', 'proses')->get();

        return Inertia::render('Master/Trolis/Produk', [
            'troli' => $troli,
            'produks' => [
                'data' => $produks,
                'total' => $produks->count()
            ],
            'availableTrolis' => $availableTrolis,
            'allProses' => $allProses,
            'filters' => $request->only(['search']),
        ]);
    }




    public function updateProses(Request $request, Troli $troli)
    {
        $request->validate(['proses_id' => 'required|exists:proses,id']);
        $troli->update(['proses_id' => $request->proses_id]);
        return back();
    }


    public function updateScan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:produk,id', // Validasi tiap ID ada di tabel
            'sudah_scan' => 'required|in:Sudah,Belum' // Memastikan hanya Sudah/Belum
        ]);

        Produk::whereIn('id', $request->ids)->update([
            'sudah_scan' => $request->sudah_scan
        ]);

        return back()->with('success', "Status scan " . count($request->ids) . " produk berhasil diubah menjadi {$request->status}.");
    }

    /**
    * Melepaskan produk dari troli (set troli_id = null)
    */
    public function removeProducts(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        Produk::whereIn('id', $request->ids)->update([
            'troli_id' => null
        ]);

        return back()->with('success', 'Produk berhasil dikeluarkan dari troli.');
    }

    /**
    * Memindahkan produk ke troli lain berdasarkan ID troli tujuan
    */
    public function moveProducts(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'troli_id' => 'required|exists:troli,id'
        ]);

        Produk::whereIn('id', $request->ids)->update([
            'troli_id' => $request->troli_id
        ]);

        return back()->with('success', 'Produk berhasil dipindahkan ke troli lain.');
    }


    public function hapusTroli(Troli $troli)
    {
        // 1. Cek apakah masih ada produk yang terikat dengan troli ini
        $adaProduk = \App\Models\Produk::where('troli_id', $troli->id)->exists();

        if ($adaProduk) {
            // Jika masih ada isinya, kirim pesan error (bisa ditangkap toast di Inertia)
            return back()->with('error', 'Troli tidak bisa direset karena masih berisi produk. Keluarkan atau pindahkan produk terlebih dahulu.');
        }

        // 2. Jika kosong, lakukan reset status troli
        $troli->update([
            'proses_id' => null,
            'status' => 'Selesai Bongkar', // Atau 'Kosong' sesuai enum kamu
            'is_output' => false,
        ]);

        return redirect()->route('master.troli.index')
            ->with('success', 'Troli berhasil dikosongkan dan dilepas dari proses.');
    }
}
