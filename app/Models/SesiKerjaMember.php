<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['sesi_kerja_id', 'user_id'])]
#[Table('sesi_kerja_member')]
class SesiKerjaMember extends Model
{
    public function sesiKerja(): BelongsTo
    {
        return $this->belongsTo(SesiKerja::class, 'sesi_kerja_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
