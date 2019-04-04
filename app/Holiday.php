<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
	use SoftDeletes;

	protected $connection = 'mysql';
	protected $dates = ['deleted_at'];
	protected $primaryKey = 'holiday_id';
}
