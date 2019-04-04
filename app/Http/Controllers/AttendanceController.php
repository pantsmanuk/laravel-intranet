<?php

namespace App\Http\Controllers;

use App\DoorEvents;
use App\EmployeeDetails;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use function foo\func;
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

		$onSite = DoorEvents::select('empref')->whereDate( 'doordate', $dtLondon->toDateString())->distinct()->get();
		$employees = EmployeeDetails::whereIn( 'empref', $onSite )->orderBy('surname')->orderBy('forenames')->get();

		$employees->map(function ($employee) {
			$dtLondon = new Carbon( 'now', 'Europe/London' );
			$employee['name'] = $employee['forenames'] . ' ' . $employee['surname'];
			$employee['doorevent'] = DoorEvents::whereDate( 'doordate', $dtLondon->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doorevent')->first();
			return $employee;
		});

    	$rows = DoorEvents::whereDate( 'doordate', $dtLondon->subWeekdays(1)->toDateString())->get();

		return view('attendance', compact('dtLondon', 'employees', 'rows'));
    }
}
