<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['shift'])]
#[Table('shift')]

class Shift extends Model
{
    public function sesi_kerja()
    {
        return $this->hasMany(SesiKerja::class, 'shift_id', 'id');
    }
}
