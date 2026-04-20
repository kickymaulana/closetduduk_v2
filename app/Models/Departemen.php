<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['departemen'])]
#[Table('departemen')]
class Departemen extends Model
{
    public function proses(): HasMany
    {
        return $this->hasMany(Proses::class, 'departemen_id', 'id');
    }

    /**
     * Jika kamu juga ingin menghitung jumlah user di departemen tersebut nantinya
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'departemen_id', 'id');
    }
}
