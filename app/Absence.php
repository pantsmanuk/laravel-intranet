<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Absence extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'mysql';
    protected $table = 'absences';
    protected $dates = [
        'start_at',
        'end_at',
    ];
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'absence_id',
        'note',
        'days_paid',
        'days_unpaid',
        'approved',
    ];
}
