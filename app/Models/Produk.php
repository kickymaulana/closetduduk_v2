<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded([])]
#[Table('produk')]

class Produk extends Model
{
    public function troli(): BelongsTo
    {
        return $this->belongsTo(Troli::class, 'troli_id');
    }

    public function pengerjaan_produks(): HasMany
    {
        return $this->hasMany(PengerjaanProduk::class, 'produk_id');
    }

    public function kualitas(): BelongsTo
    {
        return $this->belongsTo(Kualitas::class, 'kualitas_id');
    }

    public function warna(): BelongsTo
    {
        return $this->belongsTo(Warna::class, 'warna_id');
    }

}
