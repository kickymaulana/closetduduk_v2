<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\Produk;
use App\Models\PengerjaanProduk;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProdukController extends Controller
{
    public function index(Request $request, Troli $troli)
    {
        $troli->load('proses');
        // Kita ambil produk yang hanya milik troli ini
        $produks = Produk::query()
            ->where('troli_id', $troli->id)
            ->when($request->search, function ($query, $search) {
                $query->where('qrcode', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Trolis/Produk/Index', [
            'troli'   => $troli, // Kirim data detail troli-nya juga
            'produks' => $produks,
            'filters' => $request->only(['search']),
        ]);
    }

    public function scan_awal(Troli $troli)
    {
        // Load relasi jika perlu, atau kirim data troli langsung
        return Inertia::render('Trolis/Produk/ScanAwal', [
            'troli' => $troli
        ]);
    }

    public function scan_awal_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string|max:10|unique:produk,qrcode',
        ], [
            'qr.unique' => 'QR Code ini sudah pernah discan/terdaftar!',
            'qr.max' => 'Format QR Code salah (maksimal 10 karakter).'
        ]);

        $sesi_kerja_id = session('sesi_kerja_id');

        if (!$sesi_kerja_id) {
            return back()->withErrors(['error' => 'Silakan pilih/aktifkan Sesi Kerja terlebih dahulu di menu Sesi Kerja!']);
        }

        try {
            return DB::transaction(function () use ($request, $troli, $sesi_kerja_id) {

                $produk = $troli->produks()->create([
                    'qrcode' => $request->qr,
                    'nama' => 'Sample ' . $request->qr,
                    'jenis' => $troli->jenis,
                    'status_akhir' => 'OK',
                    'sudah_scan' => 'Sudah',
                ]);

                PengerjaanProduk::create([
                    'produk_id' => $produk->id,
                    'sesi_kerja_id' => $sesi_kerja_id,
                    'proses_id' => $troli->proses->id,
                ]);

                return back()->with('success', 'Scan berhasil. ');
            });

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }



        return back()->with('message', 'Produk berhasil ditambahkan.');
    }

    // 1. Method Tampilan Scan Validasi
    public function scan(Troli $troli)
    {
        return Inertia::render('Trolis/Produk/Scan', [
            'troli' => $troli
        ]);
    }

    // 2. Method Logika Scan Validasi (Update Status)
    public function scan_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string',
        ]);

        // Cari produk di dalam troli ini
        $produk = $troli->produks()->where('qrcode', $request->qr)->first();

        if (!$produk) {
            return back()->withErrors(['qr' => 'Barang ini tidak ada di dalam troli ini!']);
        }

        if ($produk->sudah_scan === 'Sudah') {
            return back()->withErrors(['qr' => 'Barang ini sudah discan sebelumnya.']);
        }

        // Update status scan saja, tidak membuat data baru
        $produk->update([
            'sudah_scan' => 'Sudah'
        ]);

        return back()->with('message', 'Validasi produk berhasil.');
    }

    public function scan_pindah(Troli $troli)
    {
        return Inertia::render('Trolis/Produk/ScanPindah', [
            // Data Troli Asal
            'troli' => $troli->load('produks'), // opsional load produks jika ingin tampilkan list barang

            // Data Daftar Troli Tujuan (untuk pilihan di sidebar/dropdown)
            'daftarTroli' => Troli::where('id', '!=', $troli->id)
                ->get(['id', 'invoice', 'keperluan'])
        ]);
    }

    public function scan_pindah_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string',
            'troli_tujuan_id' => 'required|exists:troli,id',
        ]);

        // Cari produk yang berada di troli asal (troli_id) dengan qrcode tersebut
        $produk = Produk::where('troli_id', $troli->id)
            ->where('qrcode', $request->qr)
            ->first();

        if (!$produk) {
            return back()->withErrors([
                'qr' => "Produk {$request->qr} tidak ditemukan di troli " . $troli->invoice
            ]);
        }

        // Eksekusi perpindahan ID
        $produk->update([
            'troli_id' => $request->troli_tujuan_id,
            // Optional: Jika ingin reset status scan saat pindah troli
            // 'sudah_scan' => 'Belum'
        ]);

        return back();
    }


    public function scan_hapus(Troli $troli)
    {
        return Inertia::render('Trolis/Produk/ScanHapus', [
            'troli' => $troli
        ]);
    }

    public function scan_hapus_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string',
        ]);

        // 1. Cari produk yang ada di troli ini berdasarkan QR
        $produk = Produk::where('troli_id', $troli->id)
            ->where('qrcode', $request->qr)
            ->first();

        // 2. Cek apakah produk ditemukan
        if (!$produk) {
            return back()->withErrors([
                'qr' => "Produk {$request->qr} tidak ada di troli ini."
            ]);
        }


        // 3. SYARAT KRUSIAL: Cek apakah statusnya "Buang"
        // Asumsi: kolom status bernama 'status'
        if ($produk->status_akhir !== 'Buang') {
            return back()->withErrors([
                'qr' => "Gagal! Produk {$request->qr} statusnya '{$produk->status}'. Hanya produk berstatus 'Buang' yang boleh dihapus."
            ]);
        }

        // 4. Eksekusi Hapus (atau lepas dari troli)
        // Jika maksudnya hapus permanen dari DB: $produk->delete();
        // Jika maksudnya hanya dikeluarkan dari troli:
        $produk->update(['troli_id' => null]);

        return back();
    }
}
