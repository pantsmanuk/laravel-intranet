<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Attendance;
use App\Fob;
use App\User;
use App\WorkState;
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
                    $fob_user = Fob::where('fob_id', $employee->empref)
                        ->whereDate('created_at', $dt->toDateString())
                        ->pluck('user_id')
                        ->first();
                    if (!empty($fob_user)) {
                        $employee['spare_user'] = $fob_user;
                        $employee['spare_name'] = User::find($fob_user)->name;
                    }
                    break;
            }
            if (!in_array($employee->empref, [12,13,14])) {
                $employee['name'] = $user->name;
                $employee['forenames'] = $user->forenames;
            } else {
                switch ($employee->empref) {
                    case 12:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #1';
                        break;
                    case 13:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #2';
                        break;
                    case 14:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #3';
                        break;
                }
            }
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

        $offSite = User::select('users.id', 'users.name', 'employees.default_work_state_id')
            ->join('employees', 'users.id', '=', 'employees.id')
            ->whereDate('users.deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('users.deleted_at')
            ->orderBy('users.name')
            ->get();
        $offSite->map(function ($employee) {
            $dt = Date::now()->toImmutable()->timezone('Europe/London');

            $absence = Absence::select('absence_types.name AS work_state', 'absences.note')
                ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
                ->where('absences.user_id', $employee->id)
                ->where('absences.started_at', '<=', $dt->addHour()->toDateTimeString())
                ->where('absences.ended_at', '>=', $dt->toDateTimeString())
                ->first();

            // @todo Add the started_at time to the offsite view?
            if (!is_null($absence)) {
                $employee['door_event'] = $absence->work_state;
                $employee['note'] = trim($absence->note);
            } else {
                $employee['door_event'] = WorkState::where('id', $employee->default_work_state_id)
                    ->pluck('work_state')
                    ->first();
                $employee['note'] = '';
            }

            return $employee;
        });

        $onSite_users = array_merge($onSite->pluck('empref')->toArray(), $onSite->pluck('spare_user')->toArray());
        $offSite = $offSite->filter(function ($employee) use ($onSite_users) {
            if ($employee['door_event'] === 'Holiday'
                || !in_array($employee->id, $onSite_users)) {
                return $employee;
            }
        });

        return view('attendance.index')->with(['onSite'=>$onSite, 'offSite'=>$offSite]);
    }
}
