<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Attendance;
use App\EmployeeDetails;
use App\Fob;
use App\Workstate;
use Illuminate\Foundation\Auth\User;
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
        $dtLocal = Date::now()->timezone('Europe/London');

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

        // UGLY KLUDGE
        // Get an orderByRaw() that correctly orders $employees by arrival time.
        // MSSQL has a CASE statement that functions similarly to MySQL's FIELD().
        $orderByRaw = "CASE empref";
        $i = 1;
        foreach ($onSite as $value) {
            $orderByRaw .= " WHEN " . $value['empref'] . " THEN $i";
            $i++;
        }
        $orderByRaw .= " END";
        // END UGLY KLUDGE

        // @Todo Deprecate EmployeeDetails, we are storing all this in Users now...
        $employees = EmployeeDetails::whereIn('empref', $onSite)->orderByRaw($orderByRaw)->get();
        $employees->map(function ($employee) {
            $dt = Date::now()->timezone('Europe/London');
            $employee['spare_name'] = '';
            switch ($employee['empref']) {
                case 12:
                case 13:
                case 14:
                    $name = User::where('id', Fob::where('FobID', $employee['empref'])
                        ->whereDate('created_at', Date::now('Europe/London')->toDateString())
                        ->pluck('UserID')
                        ->first())
                        ->pluck('name')
                        ->first();
                    $employee['spare_name'] = $name;
                    break;
            }
            $employee['doorevent'] = (int)Attendance::whereDate('doordate', $dt->toDateString())->where('empref', $employee->empref)
                ->latest('doortime')->select('doorevent')->first()->doorevent;
            $employee['dooreventtime'] = Attendance::whereDate('doordate', $dt->toDateString())->where('empref', $employee->empref)
                ->latest('doortime')->select('doortime')->first()->eventtime;
            $employee['firstevent'] = Attendance::whereDate('doordate', $dt->toDateString())->where('empref', $employee->empref)
                ->oldest('doortime')->select('doortime')->first()->eventtime;
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
                $employee['doorevent'] = $absence->workstate;
                $employee['note'] = $absence->note;
            } else {
                $employee['doorevent'] = Workstate::where('id', $employee->default_workstate_id)
                    ->pluck('workstate')
                    ->first();
                $employee['note'] = "";
            }

            return $employee;
        });

        return view('attendance.index', compact('dtLocal', 'employees', 'offSite', 'events'));
    }
}
