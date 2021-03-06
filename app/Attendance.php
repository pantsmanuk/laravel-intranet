<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'doorevents';
    protected $primaryKey = 'dooraccessref';

    public $timestamps = false;

    public function getAreaAttribute()
    {
        $t_area = [1 => 'Suite 33', 2 => 'Suite 32'];

        return $t_area[$this->AreaRef];
    }

    public function getEventDateAttribute()
    {
        $t_date = new carbon(substr($this->doordate, 0, 10));

        return $t_date->format('d/m/Y');
    }

    public function getEventTimeAttribute()
    {
        $t_hh = floor($this->doortime / 3600);
        $t_mm = floor($this->doortime / 60 % 60);

        return sprintf('%02d:%02d', $t_hh, $t_mm);
    }

    public function getEventTypeAttribute()
    {
        $t_event = ['In', 'Out'];

        return $t_event[$this->doorevent];
    }

    public function getSiteAttribute()
    {
        $t_site = [1 => 'AMP House'];

        return $t_site[$this->termref];
    }
}
