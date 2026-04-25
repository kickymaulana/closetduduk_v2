<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\TroliFisik;
use App\Models\Proses;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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

        // 1. Cek apakah ada produk di dalam troli yang status 'sudah_scan' nya masih 'Belum'
        $adaYangBelumScan = $troli->produks()
        ->where('sudah_scan', 'Belum')
        ->exists();

        if ($adaYangBelumScan) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'error' => 'Gagal! Masih ada produk di dalam troli ini yang belum discan.'
            ]);
        }

        $urutanSekarang = $troli->proses->urutan;

        $prosesSekarang = Proses::where('urutan', $urutanSekarang)->first();

        $prosesBerikutnya = \App\Models\Proses::where('departemen_id', $troli->proses->departemen_id)
            ->where('urutan', '>', $urutanSekarang)
            ->orderBy('urutan', 'asc')
            ->first();

        if (!$prosesBerikutnya) {
            $troli->update([
                'status' => 'Selesai',
                'is_output' => true,
            ]);
            return redirect()->route('trolis.index')->with('success', 'Troli mencapai tahap akhir dan telah diselesaikan.');
        }

        // 5. Jalankan update ke proses berikutnya
        DB::transaction(function () use ($troli, $prosesBerikutnya) {
            $troli->update([
                'status' => 'Selesai', // Atau mungkin statusnya jadi 'Pending' lagi untuk proses berikutnya?
                'proses_id' => $prosesBerikutnya->id
            ]);

            // Reset status scan produk menjadi 'Belum' agar bisa discan ulang di proses baru
            $troli->produks()->update([
                'sudah_scan' => 'Belum'
            ]);
        });

        return redirect()->route('trolis.index')->with('success', 'Troli berhasil diambil.');

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
                    ->where('status', 'Selesai') // Pastikan kolom 'status' sesuai dengan DB kamu
                    ->where('is_output', true);
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
            'is_output' => false,
        ]);
        $troli->produks()->update([
            'sudah_scan' => 'Belum'
        ]);

        return redirect()->route('trolis.index')->with('success', 'Troli berhasil diambil.');
    }


    public function kembalikan(Troli $troli)
    {
        $prosesSekarang = $troli->proses->id;
        $proses = Proses::where('urutan', '<', $prosesSekarang)->orderBy('urutan')->get();

        return Inertia::render('Trolis/Kembalikan', [
            'troli' => $troli->load('proses'),
            'prosesTujuan' => $proses,
        ]);
    }

    /**
     * Memproses perubahan data ke database (POST)
     */
    public function kembalikan_store(Request $request, Troli $troli)
    {
        // Validasi: proses_id harus ada di tabel proses
        $request->validate([
            'proses_id' => 'required|exists:proses,id',
        ]);

        try {
            // Kita gunakan Transaction agar jika satu gagal, semua batal (aman)
            \DB::transaction(function () use ($request, $troli) {

                // 1. Update status troli
                $troli->update([
                    'proses_id' => $request->proses_id,
                    'status'    => 'Proses', // Reset kembali ke 'Proses' jika sebelumnya 'Selesai'
                ]);

                // 2. Reset status produk di dalam troli tersebut
                // Karena mundur proses, biasanya produk harus di-scan ulang
                $troli->produks()->update([
                    'sudah_scan' => 'Belum'
                ]);
            });

            return redirect()->route('trolis.index')
                ->with('success', "Troli {$troli->invoice} berhasil dikembalikan.");

        } catch (\Exception $e) {
            return back()->withErrors([
                'proses_id' => 'Gagal mengembalikan troli. Silahkan coba lagi.'
            ]);
        }
    }


    // Menampilkan halaman konfirmasi hapus (Inertia/View)
    public function hapus(Troli $troli)
    {
        return inertia('Troli/Hapus', [
            'troli' => $troli->loadCount('produks')
        ]);
    }



    public function hapus_store(Troli $troli)
    {
        // 1. Cek apakah ada produk
        // Pakai withErrors agar ditangkap oleh onError di Inertia
        if ($troli->produks()->exists()) {
            return back()->withErrors([
                'error' => 'Troli tidak bisa dihapus karena masih berisi ' . $troli->produks()->count() . ' produk.'
            ]);
        }

        try {
            DB::transaction(function () use ($troli) {
                // 2. Ambil kode nomor troli fisik (4 karakter pertama)
                $nomorFisik = substr($troli->invoice, 0, 4);

                // 3. Update status di table troli_fisik
                TroliFisik::where('nomor', $nomorFisik)
                    ->update(['status' => 'Tidak']);

                // 4. Hapus data troli
                $troli->delete();
            });

            return redirect()->route('trolis.index')->with('success', 'Troli berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

}
