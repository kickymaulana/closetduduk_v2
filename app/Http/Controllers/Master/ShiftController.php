<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::query()
            ->when($request->search, function ($query, $search) {
                // Mencari berdasarkan nama shift
                $query->where('shift', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Shifts/Index', [
            'shifts' => $shifts,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Shifts/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift' => 'required|string|max:255|unique:shift,shift',
        ], [
            'shift.required' => 'Nama shift harus diisi.',
            'shift.unique' => 'Nama shift sudah terdaftar.',
        ]);

        Shift::create([
            'shift' => $request->shift,
        ]);

        return redirect()->route('shifts.index')
            ->with('message', 'Data shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return Inertia::render('Master/Shifts/Edit', [
            'shift' => $shift
        ]);
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'shift' => "required|string|max:255|unique:shift,shift,{$shift->id}",
        ], [
            'shift.required' => 'Nama shift harus diisi.',
            'shift.unique' => 'Nama shift sudah terdaftar.',
        ]);

        $shift->update([
            'shift' => $request->shift,
        ]);

        return redirect()->route('shifts.index')
            ->with('message', 'Data shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        // Cek apakah shift sedang digunakan di sesi_kerja sebelum menghapus (opsional tapi disarankan)
        if ($shift->sesi_kerja()->exists()) {
            return redirect()->back()->with('error', 'Shift tidak bisa dihapus karena sedang digunakan dalam data sesi kerja.');
        }

        $shift->delete();

        return redirect()->route('shifts.index')
            ->with('message', 'Data shift berhasil dihapus.');
    }
}
