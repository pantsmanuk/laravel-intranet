<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Telephone extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'mysql';
    protected $table = 'telephone';
    protected $fillable =[
        'name',
        'number'
    ];
}
