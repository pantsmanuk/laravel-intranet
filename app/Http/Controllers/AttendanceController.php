<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Attendance;
use App\Fob;
use App\User;
use App\Workstate;
use Illuminate\Support\Facades\Date;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dtLocal = Date::now('Europe/London');

        $active = User::select('id')
            ->whereDate('deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->get();

        // "Inject" the spare fobs so they will show up
        $active->push(['id' => 12]);
        $active->push(['id' => 13]);
        $active->push(['id' => 14]);

        $onSite = Attendance::select('empref')
            ->whereDate('doordate', $dtLocal->toDateString())
            ->whereIn('empref', $active)
            ->groupBy('empref')
            ->orderByRaw('MIN(doortime) ASC')
            ->get();
        $onSite->map(function ($employee) {
            $dt = Date::now('Europe/London');
            $user = User::find($employee->empref);

            $employee['spare_name'] = '';
            switch ($employee->empref) {
                case 12:
                case 13:
                case 14:
                    $name = User::find(Fob::where('FobID', $employee->empref)
                        ->whereDate('created_at', $dt->toDateString())
                        ->pluck('UserID')
                        ->first())
                        ->name;
                    $employee['spare_name'] = $name;
                    break;
            }
            $employee['name'] = $user->name;
            $employee['forenames'] = $user->forenames;
            $employee['door_event'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->latest('doortime')
                ->select('doorevent')
                ->first()
                ->event_type;
            $employee['door_event_time'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->latest('doortime')
                ->select('doortime')
                ->first()
                ->event_time;
            $employee['first_event'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->oldest('doortime')
                ->select('doortime')
                ->first()
                ->event_time;

            return $employee;
        });

        $offSite = User::select('users.id', 'users.name', 'employees.default_workstate_id')
            ->join('employees', 'users.id', '=', 'employees.id')
            ->whereDate('users.deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('users.deleted_at')
            ->orderBy('users.name')
            ->get();
        $here = $onSite->pluck('empref')->toArray(); // @todo This *really* needs to account for assigned spare fobs
        $offSite = $offSite->filter(function ($employee) use ($here) {
            if (!in_array($employee->id, $here)) {
                return $employee;
            }
        });
        $offSite->map(function ($employee) {
            $dt = Date::now()->timezone('Europe/London');

            $absence = Absence::select('absence_lookup.name AS workstate', 'absences.note')
                ->join('absence_lookup', 'absences.absence_id', '=', 'absence_lookup.id')
                ->where('absences.user_id', $employee->id)
                ->where('absences.start_at', '<=', $dt->toDateTimeString())
                ->where('absences.end_at', '>=', $dt->toDateTimeString())
                ->first();

            if (!is_null($absence)) {
                $employee['door_event'] = $absence->workstate;
                $employee['note'] = trim($absence->note);
            } else {
                $employee['door_event'] = Workstate::where('id', $employee->default_workstate_id)
                    ->pluck('workstate')
                    ->first();
                $employee['note'] = '';
            }

            return $employee;
        });

        return view('attendance.index')->with(['onSite'=>$onSite, 'offSite'=>$offSite]);
    }
}
