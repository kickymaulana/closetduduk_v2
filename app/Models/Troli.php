<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Guarded([])]
#[Table('troli')]

class Troli extends Model
{
    protected $appends = ['terakhir_diperbaharui', 'terakhir_diperbaharui_jam',]; // Biar otomatis muncul di JSON/Inertia

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'troli_id');
    }

    public function proses(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_id', 'id');
    }

    protected function terakhirDiperbaharui(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at?->diffForHumans(),
        );
    }

    protected function terakhirDiperbaharuiJam(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at?->translatedFormat('d F Y, H:i'),
        );
    }

}
