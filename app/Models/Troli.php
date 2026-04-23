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
    protected $appends = ['create_time', 'tanggal_jam']; // Biar otomatis muncul di JSON/Inertia

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'troli_id');
    }

    public function proses(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_id', 'id');
    }

    protected function createTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->diffForHumans(),
        );
    }

    protected function tanggalJam(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->translatedFormat('d F Y, H:i'),
        );
    }

}
