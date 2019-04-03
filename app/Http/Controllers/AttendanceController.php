<?php

namespace App\Http\Controllers;

use App\DoorEvents;
use App\EmployeeDetails;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;

class HomeController extends Controller
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
    	$dtLondon = new Carbon( 'now', 'Europe/London' );

		$onSite = DoorEvents::select('empref')->whereDate( 'doordate', $dtLondon->toDateString())->distinct()->get();
		$employees = EmployeeDetails::whereIn( 'empref', $onSite )->orderBy('surname')->orderBy('forenames')->get();

		$employees->map(function ($employee) {
			$dtLondon = new Carbon( 'now', 'Europe/London' );
			$employee['name'] = $employee['forenames'] . ' ' . $employee['surname'];
			$employee['doorevent'] = DoorEvents::whereDate( 'doordate', $dtLondon->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doorevent')->first();
			return $employee;
		});

    	$rows = DoorEvents::whereDate( 'doordate', Carbon::now('Europe/London')->subWeekdays(1)->toDateString())->get();

		return view('home', compact('dtLondon', 'employees', 'rows'));
    }
}
