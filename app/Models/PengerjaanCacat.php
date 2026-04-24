<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
#[Table('pengerjaan_cacat')]
class PengerjaanCacat extends Model
{
    public function cacat(): BelongsTo {
        return $this->belongsTo(Cacat::class, 'cacat_id');
    }

    /**
     * Relasi ke table pengerjaan_produk
     */
    public function pengerjaan_produk(): BelongsTo
    {
        return $this->belongsTo(PengerjaanProduk::class, 'pengerjaan_produk_id');
    }

    /**
     * Relasi ke user yang melakukan scan (berdasarkan user_scan_id)
     */
    public function user_scan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_scan_id');
    }

    /**
     * Relasi ke proses saat discan (berdasarkan proses_scan_id)
     */
    public function proses_scan(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_scan_id');
    }

    public function user_pj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_pj_id');
    }
    public function proses_pj(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_pj_id');
    }
}
