<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class staff extends Model
{
	protected $connection = 'mysql';
	protected $dates = ['deleted_at'];
	protected $primaryKey = 'staff_id';
}
