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
        'start_at',
        'end_at',
    ];
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
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
        $overlaps = self::select('users.name', 'start_at', 'end_at', 'approved')
            ->join('users', 'absences.user_id', '=', 'users.id')
            ->whereRaw("absences.deleted_at IS NULL
                AND (start_at BETWEEN '".$absence['start_at']."' AND '".$absence['end_at']."'
                    OR end_at BETWEEN '".$absence['start_at']."' AND '".$absence['end_at']."'
                    OR (start_at < '".$absence['start_at']."' AND end_at > '".$absence['end_at']."'))")
            ->get();

        return $overlaps;
    }
}
