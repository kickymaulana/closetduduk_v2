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
        // 1. Validasi Scan (Sudah benar)
        if ($troli->produks()->where('sudah_scan', 'Belum')->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'error' => 'Gagal! Masih ada produk di dalam troli ini yang belum discan.'
            ]);
        }

        $prosesSekarang = $troli->proses;
        // Cari proses dengan urutan tepat di atas urutan saat ini
        $prosesBerikutnya = Proses::where('urutan', '>', $prosesSekarang->urutan)
            ->orderBy('urutan', 'asc')
            ->first();

        // JIKA TIDAK ADA PROSES BERIKUTNYA (Finish Total)
        if (!$prosesBerikutnya) {
            $troli->update([
                'status' => 'Selesai',
                'is_output' => false, // Tidak bisa diambil siapa-siapa lagi
            ]);
            return redirect()->route('trolis.index')->with('success', 'Produksi selesai sepenuhnya.');
        }

        // JIKA PROSES BERIKUTNYA BEDA DEPARTEMEN
        if ($prosesBerikutnya->departemen_id != auth()->user()->departemen_id) {
            $troli->update([
                'status' => 'Selesai',
                'is_output' => true, // Muncul di menu "Ambil Troli" departemen lain
            ]);
            return redirect()->route('trolis.index')->with('success', 'Troli selesai dan siap diambil departemen berikutnya.');
        }

        // JIKA PROSES BERIKUTNYA MASIH DEPARTEMEN YANG SAMA
        // Otomatis pindah ke tahap berikutnya tanpa perlu "Ambil" manual
        DB::transaction(function () use ($troli, $prosesBerikutnya) {
            $troli->update([
                'proses_id' => $prosesBerikutnya->id,
                'status' => 'Proses', // Langsung 'Proses' karena masih di departemen yang sama
                'is_output' => false,
            ]);

            $troli->produks()->update(['sudah_scan' => 'Belum']);
        });

        return redirect()->route('trolis.index')->with('success', "Troli lanjut ke tahap: {$prosesBerikutnya->proses}");
    }



    public function ambil(Request $request)
    {
        $user = auth()->user();
        $search = $request->search;

        // AMBIL PROSES DARI SESI AKTIF (Ini Kuncinya)
        $sesiKerjaId = session('sesi_kerja_id');
        $sesiAktif = SesiKerja::find($sesiKerjaId);

        if (!$sesiAktif) {
            return redirect()->route('sesikerjas.index')->with('error', 'Aktifkan sesi kerja dulu sebelum mengambil troli.');
        }

        $prosesTargetId = $sesiAktif->proses_id;
        $urutanTarget = $sesiAktif->proses->urutan;

        // Cari proses apa yang tepat sebelum proses di sesi aktif ini
        $prosesSebelumnya = Proses::where('urutan', '<', $urutanTarget)
            ->orderBy('urutan', 'desc')
            ->first();

        $query = Troli::with(['proses'])->withCount(['produks']);

        if ($prosesSebelumnya) {
            // Troli harus berada di proses sebelumnya, status Selesai, dan siap keluar (is_output)
            $query->where('proses_id', $prosesSebelumnya->id)
                ->where('status', 'Selesai')
                ->where('is_output', true);
        } else {
            // Jika ini adalah proses urutan pertama di seluruh pabrik (Casting)
            // Maka tampilkan troli yang baru dibuat (Status: Pending / Baru)
            $query->where('status', 'Baru');
        }

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                ->orWhereHas('produks', function ($pq) use ($search) {
                    $pq->where('qrcode', 'like', "%{$search}%");
                });
            });
        }

        return Inertia::render('Trolis/Ambil', [
            'trolis' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
            'sesiAktif' => $sesiAktif
        ]);
    }


    public function ambilproses(Request $request)
    {
        $sesiAktif = SesiKerja::find(session('sesi_kerja_id'));

        if (!$sesiAktif) {
            return back()->with('error', 'Sesi kerja tidak ditemukan.');
        }

        $troli = Troli::findOrFail($request->id);

        // Update troli ke proses yang ada di sesi aktif user
        $troli->update([
            'proses_id' => $sesiAktif->proses_id,
            'status'    => 'Proses',
            'is_output' => false,
        ]);

        $troli->produks()->update(['sudah_scan' => 'Belum']);

        return redirect()->route('trolis.index')->with('success', 'Troli berhasil diambil ke proses ' . $sesiAktif->proses->proses);
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
