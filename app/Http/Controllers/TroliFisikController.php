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
        $request->validate([
            'id' => 'required|exists:troli_fisik,id',
            'proses_id' => 'required|exists:proses,id',
            'keperluan'     => 'required|in:In Proses,OK,Scan',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $troliFisik = TroliFisik::findOrFail($request->id);

                if ($troliFisik->status === 'Digunakan') {
                    return back()->with('error', 'Troli sudah sedang digunakan.');
                }

                $today = Carbon::now();

                // 1. Format Tahun & Bulan (YYYYMM) -> Contoh: 202603
                $yearMonth = $today->format('Ym');

                // 2. Hitung Counter berdasarkan Bulan ini (agar reset tiap ganti bulan)
                // Atau tetap harian? Jika ingin 0001 tiap bulan, gunakan startOfMonth
                $countThisMonth = Troli::whereMonth('created_at', $today->month)
                                    ->whereYear('created_at', $today->year)
                                    ->count();

                $nextCounter = str_pad($countThisMonth + 1, 4, '0', STR_PAD_LEFT);

                // 3. Ambil nomor troli dan pastikan 3 digit (001)
                $nomorTroli = str_pad($troliFisik->nomor, 3, '0', STR_PAD_LEFT);

                // 4. Susun Invoice (Total 16 Karakter)
                // D + 001 + CT + 202603 + 0001 = 16 karakter
                $invoice = "D" . $nomorTroli . "CT" . $yearMonth . $nextCounter;

                // 5. Update status troli fisik
                $troliFisik->update(['status' => 'Digunakan']);

                // 6. Simpan ke tabel troli
                Troli::create([
                    'invoice' => $invoice,
                    'keperluan' => $request->keperluan,
                    'jenis' => 'Body',
                    'status' => 'Proses',
                    'is_output' => true,
                    'proses_id' => $request->proses_id,
                ]);

                return redirect()->route('trolis.index')->with('success', "Berhasil! Invoice: $invoice");
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

}

