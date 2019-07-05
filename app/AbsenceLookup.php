<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AbsenceLookup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'absence_lookup';
    protected $dates = [
        'deleted_at'
    ];
    protected $fillable = [
        'name',
    ];
}
