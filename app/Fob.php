<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fob extends Model
{
    protected $connection = 'mysql';
    protected $fillable = [
        'empref',
        'staff_id',
    ];
}
