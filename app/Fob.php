<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fob extends Model
{
    protected $connection = 'mysql';
    protected $dates = [
        'date',
        'deleted_at'
    ];
    protected $table = 'realaccess_fob_lookup';
    protected $fillable = [
        'FobID',
        'UserID',
        'date',
        'MachineID',
        'deleted_at',
    ];
}
