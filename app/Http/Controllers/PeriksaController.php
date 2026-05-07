<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriksaController extends Controller
{
    /**
     * Menampilkan halaman utama menu Periksa
     */
    public function periksa()
    {
        return Inertia::render('Periksa/Periksa', [
            // Kirim backUrl jika dibutuhkan untuk tombol kembali
            'backUrl' => route('produk.index'),
        ]);
    }


    public function periksa_post(Request $request)
    {
        $request->validate([
            'qr' => 'required'
        ]);

        // Tambahkan nested eager loading: troli.proses
        $produk = Produk::with(['troli.proses'])
            ->where('qrcode', $request->qr)
            ->first();

        if (!$produk) {
            return response()->json([
                'message' => "Produk {$request->qr} tidak ditemukan!"
            ], 404);
        }

        if ($produk->sudah_scan !== 'Sudah') {
            $produk->update(['sudah_scan' => 'Sudah']);
        }

        return response()->json($produk);
    }
}
