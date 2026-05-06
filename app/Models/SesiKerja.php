<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['leader_id', 'jam_masuk', 'jam_pulang', 'jenis', 'shift_id', 'proses_id'])]
#[Table('sesi_kerja')]
class SesiKerja extends Model
{
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function sesi_kerja_members(): HasMany
    {
        return $this->hasMany(SesiKerjaMember::class, 'sesi_kerja_id');
    }

    public function pengerjaan_produks(): HasMany
    {
        return $this->hasMany(PengerjaanProduk::class, 'sesi_kerja_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
