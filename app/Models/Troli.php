<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded([])]
#[Table('troli')]

class Troli extends Model
{
    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class, 'troli_id');
    }

    public function proses()
    {
        return $this->belongsTo(Proses::class, 'proses_id', 'id');
    }

}
