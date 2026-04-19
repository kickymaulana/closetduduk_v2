<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['urutan', 'departemen'])]
#[Table('master_departemen')]
class MasterDepartemen extends Model
{
    //
}
