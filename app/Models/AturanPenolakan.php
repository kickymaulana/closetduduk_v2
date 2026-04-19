<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['master_cacat_id', 'dep_toleransi', 'dep_buang', 'dep_pemeriksa'])]
#[Table('aturan_penolakan')]
class AturanPenolakan extends Model
{
    public function mastercacat()
    {
        return $this->belongsTo(MasterCacat::class, 'master_cacat_id');
    }

    public function relasi_dep_toleransi()
    {
        return $this->belongsTo(MasterDepartemen::class, 'dep_toleransi', 'id');
    }

    public function relasi_dep_buang()
    {
        return $this->belongsTo(MasterDepartemen::class, 'dep_buang', 'id');
    }

    public function relasi_dep_pemeriksa()
    {
        return $this->belongsTo(MasterDepartemen::class, 'dep_pemeriksa', 'id');
    }
}
