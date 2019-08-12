<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Fob extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'date',
    ];
    protected $table = 'realaccess_fob_lookup';
    protected $fillable = [
        'FobID',
        'UserID',
        'date',
        'MachineID',
    ];
}
