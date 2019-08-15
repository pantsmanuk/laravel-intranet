<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Fob extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fobs_users';
    protected $fillable = [
        'fob_id',
        'user_id',
    ];
}
