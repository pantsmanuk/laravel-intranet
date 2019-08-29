<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Salary extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'salary_run';
    protected $dates = [
        'run_date',
    ];
    protected $fillable = [
        'approved',
    ];
    public $timestamps = false;
}
