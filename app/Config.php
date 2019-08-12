<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Config extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

	protected $table = 'config';
	protected $fillable = [
	    'name',
        'value',
    ];
}
