<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\EmployeeDetails;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

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
    	$dtLondon = new CarbonImmutable( 'now', 'Europe/London' );

		$onSite = Attendance::select('empref')->whereDate( 'doordate', $dtLondon->toDateString())->distinct()->get();
		$employees = EmployeeDetails::whereIn( 'empref', $onSite )->orderBy('surname')->orderBy('forenames')->get();

		$employees->map(function ($employee) {
			$dt = new Carbon( 'now', 'Europe/London' );
			$employee['name'] = $employee['forenames'] . ' ' . $employee['surname'];
			$employee['doorevent'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doorevent')->first();
			$employee['firstevent'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->oldest('dooraccessref')->select('doortime')->first()->eventtime;
			return $employee;
		});

    	$events = Attendance::whereDate( 'doordate', $dtLondon->subWeekdays(1)->toDateString())->get();

		return view('attendance', compact('dtLondon', 'employees', 'events'));
    }
}
