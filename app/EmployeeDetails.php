<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/***
 * Class EmployeeDetails
 * @package App
 *
 * Is this used?
 */
class EmployeeDetails extends Model
{
    protected $connection = 'sqlsrv2';
    protected $table = 'empdetails';
    protected $primaryKey = 'empref';

    public $timestamps = false;

    public function getNameAttribute()
    {
        return $this->forenames.' '.$this->surname;
    }
}
