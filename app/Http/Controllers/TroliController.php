<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\Proses;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

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

        $prosesSekarang = Proses::where('urutan', $urutanSekarang)->first();

        $prosesBerikutnya = \App\Models\Proses::where('departemen_id', $troli->proses->departemen_id)
            ->where('urutan', '>', $urutanSekarang)
            ->orderBy('urutan', 'asc')
            ->first();

        if (!$prosesBerikutnya) {
            $troli->update([
                'status' => 'Selesai',
                'proses_id' => $prosesSekarang->id
            ]);
            // Ini akan memicu blok onError di frontend
            throw ValidationException::withMessages([
                'proses' => 'Tidak ada proses lanjutan ditemukan setelah, tapi tetep berhasil dibuat selesai' . $troli->proses->proses,
            ]);
        }

        // Jika ada, baru jalankan update
        $troli->update([
            'status' => 'Selesai',
            'proses_id' => $prosesBerikutnya->id
        ]);

        $troli->produks()->update([
            'sudah_scan' => 'Belum'
        ]);

        return back()->with('message', 'Troli dilanjutkan ke: ' . $prosesBerikutnya->proses);
    }

    public function ambil(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil info proses user saat ini
        $prosesSekarang = $user->departemen->proses()->orderBy('urutan', 'asc')->first();
        // Jaga-jaga jika data proses belum di-setup
        if (!$prosesSekarang) {
            return back()->with('error', 'Proses untuk departemen Anda belum diatur.');
        }
        $urutanSekarang = $prosesSekarang->urutan;
        // 2. Cari ID proses yang urutannya tepat sebelum urutanSekarang
        // Ini lebih aman daripada cuma $urutanSekarang - 1 (antisipasi loncatan angka)
        $prosesSebelumnya = Proses::where('urutan', '<', $urutanSekarang)
                            ->orderBy('urutan', 'desc')
                            ->first();

        $trolis = Troli::query()
            ->with(['proses'])
            ->withCount(['produks'])
            // 3. Filter berdasarkan proses sebelumnya dan status Selesai
            ->when($prosesSebelumnya, function ($query) use ($prosesSebelumnya) {
                $query->where('proses_id', $prosesSebelumnya->id)
                    ->where('status', 'Selesai'); // Pastikan kolom 'status' sesuai dengan DB kamu
            })
            // Jika tidak ada proses sebelumnya (user di urutan pertama), tampilkan data awal atau kosongkan
            ->unless($prosesSebelumnya, function ($query) {
                // Logika jika ini adalah proses pertama di sistem
                // Misalnya: hanya tampilkan yang proses_id-nya milik dia sendiri tapi statusnya 'Baru'
                $query->whereNull('id'); // Sementara dibuat kosong jika tidak ada proses sebelumnya
            })
            ->when($request->search, function ($query, $search) {
                $query->where('invoice', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Trolis/Ambil', [
            'trolis' => $trolis,
            'filters' => $request->only(['search'])
        ]);
    }


    public function ambilproses(Request $request)
    {
        $user = auth()->user();

        // Cari data troli berdasarkan ID yang dikirim dari Vue
        $troli = Troli::findOrFail($request->id);

        // Ambil proses pertama/saat ini untuk departemen user
        $prosesSekarang = $user->departemen->proses()->orderBy('urutan', 'asc')->first();

        if (!$prosesSekarang) {
            return back()->with('error', 'Proses departemen Anda belum diatur.');
        }

        // Update status dan pindahkan ke proses user
        $troli->update([
            'proses_id' => $prosesSekarang->id,
            'status'    => 'Proses', // Menandakan sedang dikerjakan di departemen Anda
        ]);
        $troli->produks->update([
            'sudah_scan' => 'Belum'
        ]);

        return redirect()->route('trolis.index')->with('success', 'Troli berhasil diambil.');
    }

}
