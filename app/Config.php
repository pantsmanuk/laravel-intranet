<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model
{
	protected $connection = 'mysql';
	protected $table = 'config';
	protected $fillable = [
	    'name',
        'value',
    ];
}
