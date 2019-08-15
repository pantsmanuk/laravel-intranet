<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserTelephone extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'users_telephones';
    protected $fillable = [
        'user_id',
        'telephone_id',
    ];
}
