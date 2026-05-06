<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Troli;
use App\Models\TroliFisik;
use App\Models\Proses;
use App\Models\SesiKerja;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class TroliController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $user = auth()->user();

        // 1. Ambil ID sesi kerja dari session
        $sesiKerjaId = session('sesi_kerja_id');

        // 2. Cari data sesi kerjanya untuk mendapatkan proses_id
        $sesiAktif = SesiKerja::with('proses')->find($sesiKerjaId);

        $query = Troli::with(['proses'])
            ->withCount('produks');

        // 3. Filter berdasarkan proses_id dari sesi yang sedang aktif
        if ($sesiAktif) {
            $query->where('proses_id', $sesiAktif->proses_id);
        } else {
            // Opsi: Jika tidak ada sesi aktif, tampilkan berdasarkan departemen user saja (seperti kode awalmu)
            $query->whereHas('proses', function ($q) use ($user) {
                $q->where('departemen_id', $user->departemen_id);
            });

            // Atau jika ingin benar-benar kosong kalau belum pilih sesi, gunakan:
            // $query->whereRaw('1 = 0');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhereHas('produks', function ($pq) use ($search) {
                    $pq->where('qrcode', 'like', "%{$search}%");
                });
            });
        }

        $trolis = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Trolis/Index', [
            'trolis' => $trolis,
            'filters' => $request->only(['search']),
            'sesiAktif' => $sesiAktif, // Opsional: kirim data sesi aktif ke Vue untuk info di UI
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

        $prosesBerikutnya = Proses::where('urutan', $urutanSekarang + 1)->first();
        //harus di cek dulu proses dengan urutan berikutnya apakah punya departemen yang login atau tidak
        //cek apakah proses berikutnya milih departemen yang login?
        if($prosesBerikutnya->departemen_id != auth()->user()->departemen_id){
            $troli->update([
                'status' => 'Selesai',
                'is_output' => true,
            ]);
            return redirect()->route('trolis.index')->with('success', 'Troli mencapai tahap akhir dan telah diselesaikan.');
        } else {
            DB::transaction(function () use ($troli, $prosesBerikutnya) {
                $troli->update([
                    'status' => 'Selesai', // Atau mungkin statusnya jadi 'Pending' lagi untuk proses berikutnya?
                    'proses_id' => $prosesBerikutnya->id
                ]);

                $troli->produks()->update([
                    'sudah_scan' => 'Belum'
                ]);
            });
            return redirect()->route('trolis.index')->with('success', 'Troli berhasil diambil.');

        }



    }


    public function ambil(Request $request)
    {
        $user = auth()->user();
        $search = $request->search; // Ambil nilai search

        // 1. Ambil info proses user saat ini
        $prosesSekarang = $user->departemen->proses()->orderBy('urutan', 'asc')->first();

        if (!$prosesSekarang) {
            return back()->with('error', 'Proses untuk departemen Anda belum diatur.');
        }

        $urutanSekarang = $prosesSekarang->urutan;
        $prosesSebelumnya = Proses::where('urutan', '<', $urutanSekarang)
                            ->orderBy('urutan', 'desc')
                            ->first();

        $trolis = Troli::query()
            ->with(['proses'])
            ->withCount(['produks'])
            // Filter alur proses
            ->when($prosesSebelumnya, function ($query) use ($prosesSebelumnya) {
                $query->where('proses_id', $prosesSebelumnya->id)
                    ->where('status', 'Selesai')
                    ->where('is_output', true);
            })
            ->unless($prosesSebelumnya, function ($query) {
                $query->whereNull('id');
            })
            // Tambahkan Logika Search Scan di sini
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    // Cari berdasarkan invoice troli
                    $q->where('nomor', 'like', "%{$search}%")
                    // ATAU cari produk di dalam troli tersebut
                    ->orWhereHas('produks', function ($pq) use ($search) {
                        $pq->where('qrcode', 'like', "%{$search}%");
                    });
                });
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
        // 1. Cek apakah ada produk di dalamnya
        if ($troli->produks()->exists()) {
            return back()->withErrors([
                'error' => 'Troli tidak bisa dilepaskan karena masih berisi ' . $troli->produks()->count() . ' produk. Kosongkan isi troli terlebih dahulu!'
            ]);
        }

        try {
            DB::transaction(function () use ($troli) {
                // Kita tidak menghapus baris, tapi mengosongkan relasinya
                $troli->update([
                    'proses_id' => null,        // Melepas dari departemen/proses
                    'status'    => 'Proses',    // Reset ke status default
                    'keperluan' => 'OK',        // Reset ke keperluan default
                    'is_output' => false,      // Kembalikan ke status bukan wadah output
                ]);
            });

            return redirect()->route('trolis.index')->with('success', 'Troli berhasil dilepaskan dan sekarang berstatus Tersedia.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Gagal mengosongkan data: ' . $e->getMessage()
            ]);
        }
    }


    public function trolikosong(Request $request)
    {
        $user = auth()->user();

        $query = Troli::query()
            // 1. Syarat Mutlak: Tidak boleh ada produk di dalamnya
            ->whereDoesntHave('produks')
            // 2. Tambahan: Harus benar-benar tidak terikat proses (proses_id NULL)
            // Ini agar D086 yang sudah ada di departemen Anda tidak muncul lagi di sini
            ->whereNull('proses_id');

        if ($request->search) {
            $query->where('nomor', 'like', '%' . $request->search . '%');
        }

        $troliFisiks = $query->paginate(10)->withQueryString()->through(fn ($troli) => [
            'id' => $troli->id,
            'nomor' => $troli->nomor,
            'status' => 'Tersedia',
            'created_at' => $troli->created_at,
        ]);

        $prosesList = \App\Models\Proses::where('departemen_id', $user->departemen_id)
            ->orderBy('urutan', 'asc')
            ->get(['id', 'proses']);

        return Inertia::render('Trolis/TroliKosong', [
            'troliFisiks' => $troliFisiks,
            'prosesList' => $prosesList,
            'filters' => $request->only(['search']),
        ]);
    }

    public function trolikosong_store(Request $request)
    {
        // 1. Validasi input ID yang dikirim dari form
        $request->validate([
            'id' => 'required|exists:troli,id',
            'proses_id' => 'required|exists:proses,id',
            'keperluan' => 'required|in:OK,In Proses,Scan',
        ]);

        // 2. Ambil data troli yang mau diambil
        $troli = Troli::findOrFail($request->id);
        $nomorTroli = $troli->nomor; // Contoh: D001

        /**
        * 3. PENGECEKAN BERDASARKAN NOMOR:
        * Kita cek di seluruh tabel troli, apakah ada nomor yang sama (D001)
        * yang proses_id-nya TIDAK NULL (sedang dipakai).
        */
        $troliSedangDipakai = Troli::where('nomor', $nomorTroli)
            ->whereNotNull('proses_id')
            ->exists();

        if ($troliSedangDipakai) {
            return redirect()->back()->with('error', "Gagal! Nomor Troli {$nomorTroli} terdeteksi masih digunakan di proses lain. Kosongkan dulu secara fisik dan sistem sebelum mengambilnya kembali.");
        }

        // 4. Jika aman (tidak ada nomor tersebut yang sedang dipakai), baru update
        $troli->update([
            'proses_id' => $request->proses_id,
            'keperluan' => $request->keperluan,
            'status' => 'Proses',
            'is_output' => 1,
        ]);

        return redirect()->back()->with('success', "Troli {$nomorTroli} berhasil diambil.");
    }

}
