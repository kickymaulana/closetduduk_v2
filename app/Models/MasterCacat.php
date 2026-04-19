<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Fillable(['nama_cacat'])]
#[Table('master_cacat')]
class MasterCacat extends Model
{
    //
}
