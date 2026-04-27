<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturanPenolakan;
use App\Models\Cacat;
use App\Models\Proses;
use Inertia\Inertia;

class AturanPenolakanController extends Controller
{

    public function index(Request $request)
    {
        $aturanPenolakans = AturanPenolakan::query()
            // Kita join tabel proses (asumsikan nama tabelnya 'proses')
            // untuk mengurutkan berdasarkan nama proses pemeriksa
            ->select('aturan_penolakan.*') // Pastikan select id agar tidak bentrok
            ->join('proses as pemeriksa', 'aturan_penolakan.proses_pemeriksa', '=', 'pemeriksa.id')
            ->with(['cacat', 'proses_toleransi', 'proses_buang', 'proses_pemeriksa'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('cacat', function ($q) use ($search) {
                    $q->where('cacat', 'like', "%{$search}%");
                });
            })
            // Urutkan berdasarkan kolom 'proses' di tabel yang di-join
            ->orderBy('pemeriksa.proses', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/AturanPenolakans/Index', [
            'aturanPenolakans' => $aturanPenolakans,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/AturanPenolakans/Create', [
            'cacats' => Cacat::all(),
            'proses' => Proses::orderBy('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cacat_id'   => 'required|exists:cacat,id',
            'proses_toleransi'   => 'required|exists:proses,id',
            'proses_buang'       => 'required|exists:proses,id',
            'proses_pemeriksa'   => 'required|exists:proses,id',
        ]);

        AturanPenolakan::create($request->all());

        return redirect()->route('aturanpenolakans.index')->with('message', 'Aturan penolakan berhasil dibuat.');
    }

    public function edit($id)
    {
        return Inertia::render('Master/AturanPenolakans/Edit', [
            'aturan' => AturanPenolakan::findOrFail($id),
            'cacats' => Cacat::all(),
            'proses' => Proses::orderBy('created_at')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $aturan = AturanPenolakan::findOrFail($id);

        $request->validate([
            'cacat_id'   => 'required|exists:proses,id',
            'proses_toleransi'   => 'required|exists:proses,id',
            'proses_buang'       => 'required|exists:proses,id',
            'proses_pemeriksa'   => 'required|exists:proses,id',
        ]);

        $aturan->update($request->all());

        return redirect()->route('aturanpenolakans.index')->with('message', 'Aturan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        AturanPenolakan::findOrFail($id)->delete();
        return redirect()->route('aturanpenolakans.index')->with('message', 'Aturan berhasil dihapus.');
    }
}
