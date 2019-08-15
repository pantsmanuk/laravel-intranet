<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// @todo rename model/class to "UserTelephone"
class UserTelephoneLookup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    // @todo Table renamed users_telephones
    protected $table = 'users_telephones_lookup';
    protected $fillable = [
        'user_id',
        'telephone_id',
    ];
}
