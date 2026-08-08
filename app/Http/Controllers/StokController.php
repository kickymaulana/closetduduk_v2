<?php
namespace App\Http\Controllers;

use App\Models\Proses;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{

    public function index()
    {
        $stok = Proses::query()
            ->with('departemen:id,departemen')
            ->select('proses.*')
            // Subquery sederhana untuk menghitung jumlah produk di setiap proses
            ->addSelect(['total_produk' => DB::table('produk')
                ->whereColumn('produk.proses_id', 'proses.id')
                ->selectRaw('count(produk.id)')
            ])
            ->orderBy('urutan', 'asc')
            ->get();

        return Inertia::render('Stok/Index', [
            'stok' => $stok
        ]);
    }

}
