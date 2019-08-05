<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserTelephoneLookup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'mysql';
    protected $table = 'users_telephones_lookup';
    protected $fillable = [
        'user_id',
        'telephone_id',
    ];
}
