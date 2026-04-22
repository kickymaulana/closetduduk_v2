<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiKerja;
use Inertia\Inertia;
use App\Models\User;
use App\Models\MasterDepartemen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class SesiKerjaController extends Controller
{
    public function index(Request $request)
    {
        $sesikerjas = SesiKerja::query()
            ->where('leader_id', auth()->id())
            ->with(['leader', 'sesi_kerja_members.user'])
            ->withCount(['pengerjaan_produks as total_produk' => function ($query) {
                $query->select(DB::raw('count(distinct(produk_id))'));
            }])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('leader', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('jenis', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SesiKerjas/Index', [
            'sesikerjas' => $sesikerjas,
            'filters' => $request->only(['search']),
            'sesi_kerja_id' => session('sesi_kerja_id'),
        ]);
    }



    public function create()
    {
        return Inertia::render('SesiKerjas/Create', [
            'users' => User::where('id', '!=', auth()->id())->get(['id', 'name'])
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jam_masuk' => 'required|date',
            'jam_pulang' => 'nullable|date|after:jam_masuk',
            'jenis' => 'required|in:Body,Tangki',
            'user_ids'  => 'nullable|array', // ID anggota yang dipilih
            'user_ids.*'=> 'exists:users,id'
        ]);
        $validated['leader_id'] = Auth::id();


        // Gunakan Transaction agar jika simpan member gagal, sesi_kerja juga batal (aman)
        \DB::transaction(function () use ($validated) {
            $sesi = SesiKerja::create([
                'leader_id' => $validated['leader_id'],
                'jam_masuk' => $validated['jam_masuk'],
                'jam_pulang' => $validated['jam_pulang'],
                'jenis' => $validated['jenis'],
            ]);

            if (!empty($validated['user_ids'])) {
                foreach ($validated['user_ids'] as $userId) {
                    $sesi->sesi_kerja_members()->create([
                        'user_id' => $userId
                    ]);
                }
            }
        });


        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja berhasil dicatat.');
    }



    public function show(SesiKerja $sesikerja)
    {
        // Load relasi pengerjaan_produks, produk, dan prosesnya
        $sesikerja->load([
            'leader',
            'sesi_kerja_members.user',
            'pengerjaan_produks.produk',
            'pengerjaan_produks.proses'
        ]);

        // Hitung statistik berdasarkan produk fisik unik
        $stats = [
            'total_produk'    => $sesikerja->pengerjaan_produks()->distinct('produk_id')->count(),
            'total_ok'        => $sesikerja->pengerjaan_produks()->where('status_kondisi', 'OK')->distinct('produk_id')->count(),
            'total_in_proses' => $sesikerja->pengerjaan_produks()->where('status_kondisi', 'In Proses')->distinct('produk_id')->count(),
            'total_reject'    => $sesikerja->pengerjaan_produks()->where('status_kondisi', 'Buang')->distinct('produk_id')->count(),
        ];

        return Inertia::render('SesiKerjas/Show', [
            'sesikerja' => $sesikerja,
            'stats'     => $stats
        ]);
    }

    public function edit(SesiKerja $sesikerja)
    {
        // Load members supaya checkbox-nya tercentang otomatis di Vue
        $sesikerja->load('sesi_kerja_members');

        return Inertia::render('SesiKerjas/Edit', [
            'sesikerja' => $sesikerja,
            // Kirim daftar user lagi
            'users' => User::where('id', '!=', auth()->id())->get(['id', 'name'])
        ]);
    }

    public function update(Request $request, SesiKerja $sesikerja)
    {
        $validated = $request->validate([
            'jam_masuk' => 'required|date',
            'jam_pulang' => 'nullable|date|after:jam_masuk',
            'jenis' => 'required|in:Body,Tangki',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $sesikerja) {
            // Update data utama
            $sesikerja->update([
                'jam_masuk' => $validated['jam_masuk'],
                'jam_pulang' => $validated['jam_pulang'],
                'jenis' => $validated['jenis'],
            ]);

            // Update Member: Hapus yang lama, masukkan yang baru
            // (Ini cara paling aman kalau tidak pakai relation sync)
            $sesikerja->sesi_kerja_members()->delete();

            if (!empty($validated['user_ids'])) {
                foreach ($validated['user_ids'] as $userId) {
                    $sesikerja->sesi_kerja_members()->create([
                        'user_id' => $userId
                    ]);
                }
            }
        });

        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja dan tim berhasil diperbarui.');
    }

    public function aktifkan(SesiKerja $sesikerja)
    {
        session(['sesi_kerja_id' => $sesikerja->id]);

        return Redirect::route('sesikerjas.index')
            ->with('message', "Sesi {$sesikerja->jenis} diaktifkan.");
    }

    public function nonaktif()
    {
        session()->forget('sesi_kerja_id');

        return Redirect::route('sesikerjas.index')
            ->with('message', 'Sesi kerja dinonaktifkan.');
    }

    public function destroy(SesiKerja $sesikerja)
    {
        // Cek apakah sudah ada produk yang dikerjakan di sesi ini
        $adaPengerjaan = $sesikerja->pengerjaan_produks()->exists();

        if ($adaPengerjaan) {
            return back()->withErrors([
                'error' => 'Sesi tidak bisa dihapus karena sudah ada data pengerjaan produk di dalamnya!'
            ]);
        }

        DB::transaction(function () use ($sesikerja) {
            // Hapus member dulu (karena foreign key)
            $sesikerja->sesi_kerja_members()->delete();
            // Baru hapus sesi
            $sesikerja->delete();
        });

        return redirect()->route('sesikerjas.index')
            ->with('message', 'Sesi kerja berhasil dihapus.');
    }
}
