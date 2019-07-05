<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\EmployeeDetails;
use App\Absence;
use App\Fob;
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

        $active = Staff::select('empref')
            ->whereDate('deleted_at','>=',$dtLocal->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->get();

        // "Inject" the spare fobs so they will show up
        $active->push(['empref'=>12], ['empref'=>13], ['empref'=>14]);

        $onSite = Attendance::select('empref')
            ->whereDate( 'doordate', $dtLocal->toDateString())
            ->whereIn('empref', $active)
            ->groupBy('empref')
            ->orderByRaw('MIN(doortime) ASC')
            ->get();

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
		$employees->map(function ($employee) use ($active) {
			$dt = Date::now()->timezone('Europe/London');
            $employee['spare_name'] = '';
            switch ($employee['empref']) {
                case 12:
                case 13:
                case 14:
                    $name = Staff::where('empref', Fob::where('FobID',$employee['empref'])
                        ->whereDate('created_at', Date::now('Europe/London')->toDateString())
                        ->pluck('UserID')
                        ->first())
                        ->pluck('name')
                        ->first();
                    $employee['spare_name'] = $name;
                    break;
            }
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
        $here = $onSite->pluck('empref')->toArray();
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

            $absence = Absence::select('absence_lookup.name AS workstate', 'holidays.note')
                ->join('absence_lookup','holidays.absence_id','=','absence_lookup.id')
                ->where('staff_id',$employee->staff_id)
                ->where('start','<=',$dt->toDateTimeString())
                ->where('end','>=',$dt->toDateTimeString())
                ->first();

            if(!is_null($absence)) {
                $employee['doorevent'] = $absence->workstate;
                $employee['note'] = $absence->note;
            } else {
                $employee['doorevent'] = $workstate_arr[$employee->default_workstate];
                $employee['note'] = "";
            }

            return $employee;
        });

		return view('attendance', compact('dtLocal', 'employees', 'offSite', 'events'));
    }
}
