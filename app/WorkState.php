<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// @todo Rename model/class to "WorkState". Shouldn't need $table after.
class Workstate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    // @todo workstate renamed work_state
    protected $fillable = [
        'workstate',
    ];
}
