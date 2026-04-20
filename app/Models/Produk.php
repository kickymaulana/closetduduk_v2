<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Guarded;

#[Guarded([])]
#[Table('produk')]

class Produk extends Model
{
    public function troli_invoice()
    {
        return $this->belongsTo(Troli::class, 'troli_id');
    }

}
