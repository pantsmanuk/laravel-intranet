<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// @todo rename model/class "AbsenceType". Shouldn't need $table after.
class AbsenceLookup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'absence_lookup';
    protected $fillable = [
        'name',
    ];
}
