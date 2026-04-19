<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['leader_id', 'jam_masuk', 'jam_pulang', 'jenis', 'departemen_id'])]
#[Table('sesi_kerja')]
class SesiKerja extends Model
{
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function departemen()
    {
        return $this->belongsTo(MasterDepartemen::class, 'departemen_id');
    }

}
