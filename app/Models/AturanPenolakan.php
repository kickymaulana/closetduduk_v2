<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['cacat_id', 'proses_toleransi', 'proses_buang', 'proses_pemeriksa'])]
#[Table('aturan_penolakan')]
class AturanPenolakan extends Model
{
    public function cacat(): BelongsTo
    {
        return $this->belongsTo(Cacat::class, 'cacat_id');
    }

    public function proses_toleransi(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_toleransi', 'id');
    }

    public function proses_buang(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_buang', 'id');
    }

    public function proses_pemeriksa(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'proses_pemeriksa', 'id');
    }
}
