<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AbsenceLookup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'absence_lookup';
    protected $fillable = [
        'name',
    ];
}
