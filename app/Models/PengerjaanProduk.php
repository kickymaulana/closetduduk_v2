<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
#[Table('pengerjaan_produk')]
class PengerjaanProduk extends Model
{
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function sesiKerja(): BelongsTo
    {
        return $this->belongsTo(SesiKerja::class, 'sesi_kerja_id');
    }

    public function proses(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_id');
    }

    public function pengerjaan_cacats(): HasMany
    {
        return $this->hasMany(PengerjaanCacat::class, 'pengerjaan_produk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
