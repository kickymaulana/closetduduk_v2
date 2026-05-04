<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cacat;
use Inertia\Inertia;
use App\Models\Troli;
use App\Models\SesiKerja;
use Illuminate\Support\Facades\DB;
use App\Models\PengerjaanProduk;
use App\Models\AturanPenolakan;
use App\Models\Kualitas;
use App\Models\Warna;

class ScanCheckingController extends Controller
{
    public function inproses(Troli $troli)
    {
        $pilihan_cacat = Cacat::whereHas('aturan_penolakans', function ($query) use ($troli) {
            $query->where('proses_pemeriksa', $troli->proses->id);
        })
        ->select(['id', 'cacat'])
        ->distinct()
        ->get();

        return Inertia::render('Trolis/Produk/ScanCheckingInproses', [
            'troli' => $troli,
            'pilihan_cacat' => $pilihan_cacat,
            'pilihan_kualitas' => Kualitas::all(['id', 'kualitas']),
            'pilihan_warna' => Warna::all(['id', 'warna']),
        ]);
    }


    public function inproses_store(Request $request, Troli $troli)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'qr' => 'required|string',
            'cacat_ids' => 'nullable|array',
            'kualitas_id' => 'nullable|exists:kualitas,id',
            'warna_id' => 'nullable|exists:warna,id',
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
                    'status_akhir' => 'In Proses',
                    'kualitas_id' => $request->kualitas_id,
                    'warna_id' => $request->warna_id,
                ]);
            });

            return back()->with('success', "Produk {$request->qr} berhasil diproses.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

}
