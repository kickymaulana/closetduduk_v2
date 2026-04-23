<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TroliFisik;
use App\Models\Troli;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TroliFisikController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $troliFisiks = TroliFisik::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nomor', 'like', "%{$search}%");
            })
            ->orderByRaw("FIELD(status, 'Tidak', 'Digunakan')")
            ->orderBy('nomor', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Ambil proses yang hanya dimiliki oleh departemen user yang login
        $prosesList = \App\Models\Proses::where('departemen_id', $user->departemen_id)
            ->orderBy('urutan', 'asc')
            ->get();

        return Inertia::render('TroliFisiks/Index', [
            'troliFisiks' => $troliFisiks,
            'prosesList' => $prosesList, // Kirim daftar proses ke Vue
            'filters' => $request->only(['search'])
        ]);
    }



    public function ambil(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'id' => 'required|exists:troli_fisik,id',
            'proses_id' => 'required|exists:proses,id',
            'keperluan' => 'required|in:In Proses,OK,Scan',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Cari data troli fisik
                $troliFisik = TroliFisik::findOrFail($request->id);

                // Cek jika status sudah digunakan (Double-check untuk concurrency)
                if ($troliFisik->status === 'Digunakan') {
                    return back()->with('error', 'Troli sudah sedang digunakan.');
                }

                $today = Carbon::now();

                // 3. Format Tahun & Bulan (YYYYMM) -> 6 karakter
                $yearMonth = $today->format('Ym');

                // 4. Hitung Counter bulanan
                $countThisMonth = Troli::whereMonth('created_at', $today->month)
                                        ->whereYear('created_at', $today->year)
                                        ->count();

                $nextCounter = str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);

                // 5. Sanitasi Nomor Troli
                // Jika isi $troliFisik->nomor adalah "D002", kita ambil "002" saja
                $hanyaAngka = preg_replace('/[^0-9]/', '', $troliFisik->nomor);
                $nomorTroliClean = str_pad($hanyaAngka, 3, '0', STR_PAD_LEFT);

                // 6. Susun Invoice (Total 16 Karakter)
                // Format: [D] + [001] + [CT] + [202603] + [0001]
                // Panjang: 1 + 3 + 2 + 6 + 4 = 16
                $invoice = "D" . $nomorTroliClean . "CT" . $yearMonth . $nextCounter;

                // 7. Update status troli fisik menjadi Digunakan
                $troliFisik->update(['status' => 'Digunakan']);

                // 8. Simpan record ke tabel troli
                Troli::create([
                    'invoice'   => $invoice,
                    'keperluan' => $request->keperluan,
                    'jenis'     => 'Body',
                    'status'    => 'Proses',
                    'is_output' => false,
                    'proses_id' => $request->proses_id,
                ]);

                return redirect()->route('trolis.index')->with('success', "Berhasil! Invoice: $invoice");
            });
        } catch (\Exception $e) {
            // Log error jika diperlukan: Log::error($e->getMessage());
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }


}

