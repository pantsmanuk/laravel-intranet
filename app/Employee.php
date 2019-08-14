<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Employee extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'started_at',
        'ended_at',
    ];
    protected $fillable = [
        'started_at',
        'ended_at',
        'holiday_entitlement',
        'holiday_carried_forward',
        'days_per_week',
        'default_workstate_id'
    ];
}
