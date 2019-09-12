<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Config extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'config';
    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * Return the configuration value for a given key.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getValue($name)
    {
        return self::where('name', $name)->pluck('value')->implode('');
    }
}
