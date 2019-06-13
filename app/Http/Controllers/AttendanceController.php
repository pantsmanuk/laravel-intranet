<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\EmployeeDetails;
use App\Absence;
use App\Staff;
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

        $onSite = Attendance::select('empref')
            ->whereDate( 'doordate', $dtLocal->toDateString())
            ->groupBy('empref')
            ->orderByRaw('MIN(doortime) ASC')
            ->get();

        $here = $onSite->pluck('empref')->toArray();

        // UGLY KLUDGE
        // Get an orderByRaw() that correctly orders $employees by arrival time.
        // MSSQL has a CASE statement that functions similarly to MySQL's FIELD().
        $orderByRaw = "CASE empref";
        $i=1;
        foreach($onSite as $value){
            $orderByRaw .= " WHEN ".$value['empref']." THEN $i";
            $i++;
        }
        $orderByRaw .= " END";
        // END UGLY KLUDGE

        $employees = EmployeeDetails::whereIn('empref', $onSite)->orderByRaw($orderByRaw)->get();
		$employees->map(function ($employee) {
			$dt = Date::now()->timezone('Europe/London');
			$employee['name'] = $employee['forenames'] . ' ' . $employee['surname'];
			$employee['doorevent'] = (int) Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->latest('doortime')->select('doorevent')->first()->doorevent;
			$employee['dooreventtime'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->latest('doortime')->select('doortime')->first()->eventtime;
			$employee['firstevent'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->oldest('doortime')->select('doortime')->first()->eventtime;
			return $employee;
		});

        $offSite = Staff::select('staff_id', 'name', 'empref', 'default_workstate')
            ->whereDate('deleted_at','>=',$dtLocal->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('surname, firstname')
            ->get();
        $offSite = $offSite->filter(function($employee) use ($here) {
            if(!in_array($employee->empref, $here)) {
                return $employee;
            }
        });
        $offSite->map(function ($employee) {
            $workstate_arr = array(1=>"On-site",
                2=>"Remote working",
                3=>"Not working");
            $dt = Date::now()->timezone('Europe/London');

            $absence = Absence::select('absence_lookup.name AS workstate')
                ->join('absence_lookup','holidays.absence_id','=','absence_lookup.id')
                ->where('staff_id',$employee->staff_id)
                ->where('start','<=',$dt->toDateTimeString())
                ->where('end','>=',$dt->toDateTimeString())
                ->first();

            if(!is_null($absence)) {
                $employee['doorevent'] = $absence->workstate;
            } else {
                $employee['doorevent'] = $workstate_arr[$employee->default_workstate];
            }

            return $employee;
        });

    	$events = Attendance::whereDate( 'doordate', $dtLocal->subWeekdays(1)->toDateString())->get();

		return view('attendance', compact('dtLocal', 'employees', 'offSite', 'events'));
    }
}
