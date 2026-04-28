<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Proses;

class ProsesProduksiController extends Controller
{
    public function index()
    {
        // Mengambil semua data tanpa pagination, diurutkan berdasarkan kolom 'urutan'
        $proses = Proses::query()
            ->with('departemen:id,departemen')
            ->orderBy('urutan', 'asc')
            ->get();

        return Inertia::render('ProsesProduksi/Index', [
            'proses' => $proses,
        ]);
    }
}
