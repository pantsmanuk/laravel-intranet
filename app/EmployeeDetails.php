<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetails extends Model
{
	protected $connection = 'sqlsrv2';
	protected $table = 'empdetails';
	protected $primaryKey = 'empref';

	public $timestamps = false;

	public function getNameAttribute() {
		return $this->forenames . ' ' . $this->surname;
	}
}
