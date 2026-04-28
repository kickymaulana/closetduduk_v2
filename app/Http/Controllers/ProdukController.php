<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\Produk;
use App\Models\Cacat;
use App\Models\PengerjaanProduk;
use App\Models\SesiKerja;
use App\Models\AturanPenolakan;
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
            'qr' => [
                'required', 'string', 'size:10', 'regex:/^[A-Z0-9]+$/', 'unique:produk,qrcode'
            ],
            // Tambahkan validasi baru
            'nomor_mesin' => 'required|string',
            'nomor_mould' => 'required|string',
            'asal_slip'   => 'required|string',
        ], [
            'qr.unique' => 'QR Code ini sudah terdaftar.',
            'nomor_mesin.required' => 'Pilih nomor mesin!',
            'nomor_mould.required' => 'Pilih nomor mould!',
            'asal_slip.required'   => 'Pilih asal slip!',
        ]);

        $sesi_kerja_id = session('sesi_kerja_id');
        $qrUpper = strtoupper($request->qr);

        if (!$sesi_kerja_id) {
            return back()->withErrors(['error' => 'Silakan pilih/aktifkan Sesi Kerja terlebih dahulu di menu Sesi Kerja!']);
        }

        // Ambil data sesi beserta semua anggotanya
        $sesi = SesiKerja::with('sesi_kerja_members')->find($sesi_kerja_id);
        if (!$sesi) {
            // Jika data tidak ketemu di DB, hapus session yang basi dan minta user pilih ulang
            session()->forget('sesi_kerja_id');
            return back()->withErrors(['error' => 'Sesi kerja tidak ditemukan. Silakan pilih kembali sesi kerja yang aktif.']);
        }

        try {
            return DB::transaction(function () use ($request, $troli, $sesi) {

                // 1. Simpan Produk Utamanya
                $produk = $troli->produks()->create([
                    'qrcode' => $request->qr,
                    'nama' => 'Sample ' . $request->qr,
                    'jenis' => $troli->jenis,
                    'status_akhir' => 'OK',
                    'sudah_scan' => 'Sudah',
                    // Data tambahan dari input user
                    'nomor_mesin' => $request->nomor_mesin,
                    'nomor_mould' => $request->nomor_mould,
                    'asal_slip'   => $request->asal_slip,
                ]);

                // 2. Catat untuk Leader (yang sedang login/melakukan scan)
                PengerjaanProduk::create([
                    'produk_id' => $produk->id,
                    'sesi_kerja_id' => $sesi->id,
                    'user_id' => auth()->id(), // Leader
                    'proses_id' => $troli->proses->id,
                    'status_kondisi' => 'OK',
                ]);

                // 3. AUTO-INSERT untuk semua anggota tim
                foreach ($sesi->sesi_kerja_members as $member) {
                    PengerjaanProduk::create([
                        'produk_id' => $produk->id,
                        'sesi_kerja_id' => $sesi->id,
                        'user_id' => $member->user_id, // Anggota
                        'proses_id' => $troli->proses->id,
                        'status_kondisi' => 'OK',
                    ]);
                }

                return back()->with('success', 'Berhasil! Produk dicatat untuk Leader dan ' . $sesi->sesi_kerja_members->count() . ' anggota tim.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }

    }

    // 1. Method Tampilan Scan Validasi
    public function scan(Troli $troli)
    {
        return Inertia::render('Trolis/Produk/Scan', [
            'troli' => $troli
        ]);
    }


    public function scan_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string',
        ]);

        // 1. Cek Sesi Kerja Aktif
        $sesi_kerja_id = session('sesi_kerja_id');
        if (!$sesi_kerja_id) {
            return back()->withErrors(['error' => 'Silakan pilih/aktifkan Sesi Kerja terlebih dahulu!']);
        }

        $sesi = SesiKerja::with('sesi_kerja_members')->find($sesi_kerja_id);
        if (!$sesi) {
            session()->forget('sesi_kerja_id');
            return back()->withErrors(['error' => 'Sesi kerja tidak valid. Pilih ulang sesi kerja.']);
        }

        // 2. Cari produk di dalam troli ini
        $produk = $troli->produks()->where('qrcode', $request->qr)->first();

        if (!$produk) {
            return back()->withErrors(['qr' => 'Barang ini tidak ada di dalam troli ini!']);
        }

        if ($produk->sudah_scan === 'Sudah') {
            return back()->withErrors(['qr' => 'Barang ini sudah discan sebelumnya.']);
        }

        try {
            return DB::transaction(function () use ($produk, $troli, $sesi) {
                // 3. Update status scan produk
                $produk->update([
                    'sudah_scan' => 'Sudah'
                ]);

                // 4. Catat histori untuk LEADER
                PengerjaanProduk::create([
                    'produk_id' => $produk->id,
                    'sesi_kerja_id' => $sesi->id,
                    'user_id' => auth()->id(),
                    'proses_id' => $troli->proses->id,
                    'status_kondisi' => 'OK', // Default OK untuk scan validasi
                ]);

                // 5. Catat histori untuk SEMUA ANGGOTA
                foreach ($sesi->sesi_kerja_members as $member) {
                    PengerjaanProduk::create([
                        'produk_id' => $produk->id,
                        'sesi_kerja_id' => $sesi->id,
                        'user_id' => $member->user_id,
                        'proses_id' => $troli->proses->id,
                        'status_kondisi' => 'OK',
                    ]);
                }

                return back()->with('success', 'Validasi berhasil! Data tercatat untuk tim.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan validasi: ' . $e->getMessage()]);
        }
    }



    public function scan_pindah(Troli $troli)
    {
        // Kita hanya kirim data troli asal saja
        return Inertia::render('Trolis/Produk/ScanPindah', [
            'troli' => $troli->load('produks'),
        ]);
    }

    public function scan_pindah_store(Request $request, Troli $troli)
    {
        // Pastikan nama field di sini 'invoice_tujuan' sesuai dengan di Vue form
        $request->validate([
            'qr' => 'required|string',
            'nomor_tujuan' => 'required|string|exists:troli,nomor',
        ], [
            'nomor_tujuan.required' => 'Nomor tujuan belum diisi/tempel!',
            'nomor_tujuan.exists'   => 'Nomor troli tujuan tidak terdaftar!',
        ]);

        // 1. Ambil data troli tujuan berdasarkan invoice
        $troliTujuan = Troli::where('nomor', $request->nomor_tujuan)->first();

        // 2. Validasi: Jangan sampai pindah ke troli yang sama
        if ($troliTujuan->id === $troli->id) {
            return back()->withErrors(['nomor_tujuan' => 'Troli tujuan tidak boleh sama dengan asal!']);
        }

        // 3. Cari produk di troli asal (berdasarkan qrcode)
        $produk = Produk::where('troli_id', $troli->id)
                        ->where('qrcode', $request->qr)
                        ->first();

        if (!$produk) {
            return back()->withErrors([
                'qr' => "Produk {$request->qr} tidak ditemukan di troli " . $troli->invoice
            ]);
        }

        // 4. Update pindah troli
        $produk->update([
            'troli_id' => $troliTujuan->id,
        ]);

        // Berhasil, kembali ke halaman tadi
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


    public function scan_inproses(Troli $troli)
    {
        $pilihan_cacat = Cacat::whereHas('aturan_penolakans', function ($query) use ($troli) {
            $query->where('proses_pemeriksa', $troli->proses->id);
        })
        ->select(['id', 'cacat'])
        ->distinct()
        ->get();

        return Inertia::render('Trolis/Produk/ScanInproses', [
            'troli' => $troli,
            'pilihan_cacat' => $pilihan_cacat
        ]);
    }


    public function scan_inproses_store(Request $request, Troli $troli)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'qr' => 'required|string',
            'cacat_ids' => 'nullable|array',
        ]);

        // 2. Validasi: Cek Sesi Kerja Aktif
        $sesi_kerja_id = session('sesi_kerja_id');
        if (!$sesi_kerja_id) {
            return back()->withErrors(['error' => 'Silakan pilih/aktifkan Sesi Kerja terlebih dahulu!']);
        }

        $sesi = SesiKerja::with('sesi_kerja_members')->find($sesi_kerja_id);
        if (!$sesi) {
            session()->forget('sesi_kerja_id');
            return back()->withErrors(['error' => 'Sesi kerja tidak ditemukan.']);
        }

        // 3. Validasi: Keberadaan Produk di Troli
        $produk = $troli->produks()->where('qrcode', $request->qr)->first();
        if (!$produk) {
            return back()->withErrors(['qr' => "Produk {$request->qr} tidak ditemukan di dalam troli ini ({$troli->invoice})!"]);
        }

        // 4. Validasi: Cek Status Scan di Tabel Produk
        // Jika kolom 'sudah_scan' di tabel produk sudah 'Sudah', maka ditolak
        if ($produk->sudah_scan === 'Sudah') {
            return back()->withErrors(['qr' => "Produk {$request->qr} sudah discan sebelumnya!"]);
        }

        // Tentukan status kondisi untuk pengerjaan_produk
        $statusKondisi = !empty($request->cacat_ids) ? 'In Proses' : 'OK';

        try {
            DB::transaction(function () use ($request, $troli, $sesi, $produk, $statusKondisi) {

                // 5. Simpan Pengerjaan untuk Leader (Pencatat Utama)
                $pengerjaanLeader = PengerjaanProduk::create([
                    'user_id' => auth()->id(),
                    'produk_id' => $produk->id,
                    'sesi_kerja_id' => $sesi->id,
                    'proses_id' => $troli->proses->id,
                    'status_kondisi' => $statusKondisi,
                ]);

                // 6. Loop Cacat & Tracking Penanggung Jawab (PJ) Otomatis
                if (!empty($request->cacat_ids)) {
                    foreach ($request->cacat_ids as $cid) {

                        // Cari Aturan Penolakan
                        $aturan = AturanPenolakan::where('cacat_id', $cid)
                                    ->where('proses_pemeriksa', $troli->proses->id)
                                    ->first();

                        $userPJId = null;
                        $prosesPJId = null;

                        if ($aturan) {
                            $prosesPJId = $aturan->proses_toleransi;

                            // CARI USER PJ: Cari orang terakhir yang mengerjakan produk ini di proses PJ tersebut
                            $lastJob = PengerjaanProduk::where('produk_id', $produk->id)
                                        ->where('proses_id', $prosesPJId)
                                        ->latest('id')
                                        ->first();

                            $userPJId = $lastJob ? $lastJob->user_id : null;
                        }

                        // Simpan detail temuan ke tabel pengerjaan_cacat
                        $pengerjaanLeader->pengerjaan_cacats()->create([
                            'cacat_id' => $cid,
                            'user_scan_id' => auth()->id(),
                            'proses_scan_id' => $troli->proses->id,
                            'user_pj_id' => $userPJId,
                            'proses_pj_id' => $prosesPJId,
                        ]);
                    }
                }

                // 7. Auto-Insert Pengerjaan untuk Semua Anggota Tim
                foreach ($sesi->sesi_kerja_members as $member) {
                    PengerjaanProduk::create([
                        'user_id' => $member->user_id,
                        'produk_id' => $produk->id,
                        'sesi_kerja_id' => $sesi->id,
                        'proses_id' => $troli->proses->id,
                        'status_kondisi' => $statusKondisi,
                    ]);
                }

                // 8. Update data Produk Utama
                // Kita update status scan dan juga status akhir barang (OK/NG)
                $produk->update([
                    'sudah_scan' => 'Sudah',
                    'status_akhir' => 'In Proses'
                ]);
            });

            return back()->with('success', "Produk {$request->qr} berhasil diproses.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function scan_buang(Troli $troli)
    {
        // Ambil pilihan cacat yang memang memiliki aturan 'buang' untuk proses ini
        $pilihan_cacat = Cacat::whereHas('aturan_penolakans', function ($query) use ($troli) {
            $query->where('proses_pemeriksa', $troli->proses->id);
        })
        ->select(['id', 'cacat'])
        ->distinct()
        ->get();

        return Inertia::render('Trolis/Produk/ScanBuang', [
            'troli' => $troli,
            'pilihan_cacat' => $pilihan_cacat
        ]);
    }

    public function scan_buang_store(Request $request, Troli $troli)
    {
        $request->validate([
            'qr' => 'required|string',
            'cacat_ids' => 'required|array|min:1', // WAJIB ada minimal 1 cacat kalau BUANG
        ], [
            'cacat_ids.required' => 'Wajib memilih minimal satu jenis cacat untuk membuang produk!'
        ]);

        $sesi_kerja_id = session('sesi_kerja_id');
        if (!$sesi_kerja_id) {
            return back()->withErrors(['error' => 'Silakan aktifkan Sesi Kerja terlebih dahulu!']);
        }

        $sesi = SesiKerja::with('sesi_kerja_members')->find($sesi_kerja_id);

        $produk = $troli->produks()->where('qrcode', $request->qr)->first();
        if (!$produk) {
            return back()->withErrors(['qr' => "Produk {$request->qr} tidak ditemukan di troli ini!"]);
        }

        if ($produk->sudah_scan === 'Sudah') {
            return back()->withErrors(['qr' => "Produk {$request->qr} sudah discan sebelumnya!"]);
        }

        try {
            DB::transaction(function () use ($request, $troli, $sesi, $produk) {

                // 1. Simpan Pengerjaan untuk Leader (Status: Buang)
                $pengerjaanLeader = PengerjaanProduk::create([
                    'user_id' => auth()->id(),
                    'produk_id' => $produk->id,
                    'sesi_kerja_id' => $sesi->id,
                    'proses_id' => $troli->proses->id,
                    'status_kondisi' => 'Buang',
                ]);

                // 2. Loop Cacat & Tracking PJ (Ambil dari proses_buang)
                foreach ($request->cacat_ids as $cid) {
                    $aturan = AturanPenolakan::where('cacat_id', $cid)
                                ->where('proses_pemeriksa', $troli->proses->id)
                                ->first();

                    $userPJId = null;
                    $prosesPJId = null;

                    if ($aturan) {
                        // BEDANYA DISINI: Ambil PROSES BUANG sebagai PJ
                        $prosesPJId = $aturan->proses_buang;

                        $lastJob = PengerjaanProduk::where('produk_id', $produk->id)
                                    ->where('proses_id', $prosesPJId)
                                    ->latest('id')
                                    ->first();

                        $userPJId = $lastJob ? $lastJob->user_id : null;
                    }

                    $pengerjaanLeader->pengerjaan_cacats()->create([
                        'cacat_id' => $cid,
                        'user_scan_id' => auth()->id(),
                        'proses_scan_id' => $troli->proses->id,
                        'user_pj_id' => $userPJId,
                        'proses_pj_id' => $prosesPJId,
                    ]);
                }

                // 3. Auto-Insert Anggota Tim
                foreach ($sesi->sesi_kerja_members as $member) {
                    PengerjaanProduk::create([
                        'user_id' => $member->user_id,
                        'produk_id' => $produk->id,
                        'sesi_kerja_id' => $sesi->id,
                        'proses_id' => $troli->proses->id,
                        'status_kondisi' => 'Buang',
                    ]);
                }

                // 4. Update data Produk (Sudah Scan & Status Akhir: Buang)
                $produk->update([
                    'sudah_scan' => 'Sudah',
                    'status_akhir' => 'Buang'
                ]);
            });

            return back()->with('success', "Produk {$request->qr} BERHASIL DIBUANG.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }


    public function show($id)
    {
        $produk = Produk::with([
            'pengerjaan_produks' => function($query) {
                $query->with(['proses', 'user', 'pengerjaan_cacats.cacat'])
                    ->orderBy('created_at', 'desc'); // History terbaru di atas
            },
            'troli'
        ])->findOrFail($id);

        return Inertia::render('Produk/Show', [
            'produk' => $produk
        ]);
    }


    public function dataprodukindex(Request $request)
    {
        $search = $request->search;

        $query = Produk::query()
            ->with(['troli.proses']) // Load relasi troli dan prosesnya
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                // 1. Cari berdasarkan QR Code Produk (Scan Langsung)
                $q->where('qrcode', 'like', "%{$search}%")
                // 2. Cari berdasarkan Nama Produk
                ->orWhere('nama', 'like', "%{$search}%")
                // 3. Cari berdasarkan Invoice Troli-nya (Relasi)
                ->orWhereHas('troli', function ($tq) use ($search) {
                    $tq->where('nomor', 'like', "%{$search}%");
                });
            });
        }

        $produks = $query->paginate(15)->withQueryString();

        return Inertia::render('Produk/Index', [
            'produks' => $produks,
            'filters' => $request->only(['search']),
        ]);
    }

}
