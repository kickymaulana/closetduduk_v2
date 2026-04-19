<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturanPenolakan;
use App\Models\MasterCacat;
use App\Models\MasterDepartemen;
use Inertia\Inertia;

class AturanPenolakanController extends Controller
{
    public function index(Request $request)
    {
        $aturanPenolakans = AturanPenolakan::query()
            ->with(['mastercacat', 'relasi_dep_toleransi', 'relasi_dep_buang', 'relasi_dep_pemeriksa'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('mastercacat', function ($q) use ($search) {
                    $q->where('nama_cacat', 'like', "%{$search}%");
                });
            })
            ->latest()
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
            'cacats' => MasterCacat::all(),
            'departemens' => MasterDepartemen::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_cacat_id' => 'required|exists:master_cacat,id|unique:aturan_penolakan,master_cacat_id',
            'dep_toleransi'   => 'required|exists:master_departemen,id',
            'dep_buang'       => 'required|exists:master_departemen,id',
            'dep_pemeriksa'   => 'required|exists:master_departemen,id',
        ], [
            'master_cacat_id.unique' => 'Aturan untuk jenis cacat ini sudah ada.',
        ]);

        AturanPenolakan::create($request->all());

        return redirect()->route('aturanpenolakans.index')->with('message', 'Aturan penolakan berhasil dibuat.');
    }

    public function edit($id)
    {
        return Inertia::render('Master/AturanPenolakans/Edit', [
            'aturan' => AturanPenolakan::findOrFail($id),
            'cacats' => MasterCacat::all(),
            'departemens' => MasterDepartemen::orderBy('urutan')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $aturan = AturanPenolakan::findOrFail($id);

        $request->validate([
            'master_cacat_id' => 'required|exists:master_cacat,id|unique:aturan_penolakan,master_cacat_id,' . $aturan->id,
            'dep_toleransi'   => 'required|exists:master_departemen,id',
            'dep_buang'       => 'required|exists:master_departemen,id',
            'dep_pemeriksa'   => 'required|exists:master_departemen,id',
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
