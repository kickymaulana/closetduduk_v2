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

        $availableTrolis = Troli::where('id', '!=', $troli->id)
            ->whereNotNull('proses_id')
            ->select('id', 'nomor')
            ->get();

        // TAMBAHKAN INI: Ambil semua daftar proses untuk pilihan ganti proses
        $allProses = \App\Models\Proses::select('id', 'proses')->get();

        return Inertia::render('Master/Trolis/Produk', [
            'troli' => $troli,
            'produks' => $produks,
            'availableTrolis' => $availableTrolis,
            'allProses' => $allProses, // Kirim ke Vue
            'filters' => $request->only(['search']),
        ]);
    }


    public function updateProses(Request $request, Troli $troli)
    {
        $request->validate(['proses_id' => 'required|exists:proses,id']);
        $troli->update(['proses_id' => $request->proses_id]);
        return back();
    }



    /**
    * Update kolom sudah_scan pada produk yang dipilih
    */
    public function updateScan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|string' // "Sudah" atau "Belum"
        ]);

        Produk::whereIn('id', $request->ids)->update([
            'sudah_scan' => $request->status
        ]);

        return back()->with('success', 'Status scan berhasil diperbaharui.');
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
            'troli_id' => 'required|exists:trolis,id'
        ]);

        Produk::whereIn('id', $request->ids)->update([
            'troli_id' => $request->troli_id
        ]);

        return back()->with('success', 'Produk berhasil dipindahkan ke troli lain.');
    }

    /**
    * Menghapus/Kosongkan troli (set proses_id = null)
    */
    public function hapusTroli(Troli $troli)
    {
        // Opsional: Cek apakah troli masih ada isinya?
        // Jika ingin paksa kosongkan, kita null-kan dulu produknya
        DB::transaction(function () use ($troli) {
            Produk::where('troli_id', $troli->id)->update(['troli_id' => null]);

            $troli->update([
                'proses_id' => null,
                'status' => 'Kosong', // Sesuaikan dengan enum status kamu
            ]);
        });

        return redirect()->route('master.troli.index')
            ->with('success', 'Troli berhasil dikosongkan dan dilepas dari proses.');
    }
}
