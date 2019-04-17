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
			$employee['doorevent'] = (int) Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doorevent')->first()->doorevent;
			$employee['dooreventtime'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doortime')->first()->eventtime;
			$employee['firstevent'] = Attendance::whereDate( 'doordate', $dt->toDateString())->where( 'empref',$employee->empref)
				->oldest('dooraccessref')->select('doortime')->first()->eventtime;
			return $employee;
		});

		// @todo What about off-site staff? Can I append static records to begin with?
		$offSite = collect([
			//['name'=>'Kiran Dower', 'doorevent'=>'1'],
			['name'=>'Roger Gill-Carey', 'doorevent'=>'0'],
			['name'=>'John Hart', 'doorevent'=>'2'],
			['name'=>'Dmitry Kuznetsov', 'doorevent'=>'0'],
			['name'=>'Prim Maxwell', 'doorevent'=>'1'],
			['name'=>'Tim Maxwell', 'doorevent'=>'1'],
			]);
		//$employees->push(['attributes'=>]);
		//dd($employees);

    	$events = Attendance::whereDate( 'doordate', $dtLondon->subWeekdays(1)->toDateString())->get();

		return view('attendance', compact('dtLondon', 'employees', 'offSite', 'events'));
    }
}
