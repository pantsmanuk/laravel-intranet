<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Absence extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'started_at',
        'ended_at',
    ];
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'absence_id',
        'note',
        'days_paid',
        'days_unpaid',
        'approved',
    ];

    /***
     * @param array $absence
     * @return Collection Eloquent database collection
     */
    public static function overlaps(array $absence)
    {
        $overlaps = self::select('users.name', 'started_at', 'ended_at', 'approved')
            ->join('users', 'absences.user_id', '=', 'users.id')
            ->whereRaw("absences.deleted_at IS NULL
                AND (started_at BETWEEN '".$absence['started_at']."' AND '".$absence['ended_at']."'
                    OR ended_at BETWEEN '".$absence['started_at']."' AND '".$absence['ended_at']."'
                    OR (started_at < '".$absence['started_at']."' AND ended_at > '".$absence['ended_at']."'))")
            ->get();

        return $overlaps;
    }
}
