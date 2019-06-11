<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absence extends Model
{
	use SoftDeletes;

	protected $connection = 'mysql';
    protected $table = 'holidays';
	protected $dates = ['start','end','deleted_at'];
	protected $fillable = [
		'staff_id',
		'start',
		'end',
		'holiday_type',
		'note',
		'days_paid',
		'days_unpaid',
		'confirmed', // I don't care
		'approved',
		'deleted', // I don't care
		'nonce',
		'machine_id'];
	protected $primaryKey = 'holiday_id';
}
