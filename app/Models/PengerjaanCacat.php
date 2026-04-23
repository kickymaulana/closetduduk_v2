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
    public function master_cacat(): BelongsTo {
        return $this->belongsTo(Cacat::class, 'cacat_id');
    }
}
