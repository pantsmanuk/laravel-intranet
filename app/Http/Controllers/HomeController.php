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
		$reportDate = $dtLondon->toDateString();

		$onSite = DoorEvents::select('empref')->whereDate( 'doordate', $dtLondon->toDateString())->distinct()->get();
		$employees = EmployeeDetails::whereIn( 'empref', $onSite )->orderBy('surname')->orderBy('forenames')->get();

		$employees->map(function ($employee) {
			$dtLondon = new Carbon( 'now', 'Europe/London' );
			$employee['name'] = $employee['forenames'] . ' ' . $employee['surname'];
			$employee['doorevent'] = DoorEvents::whereDate( 'doordate', $dtLondon->toDateString())->where( 'empref',$employee->empref)
				->latest('dooraccessref')->select('doorevent')->first();
			return $employee;
		});

    	$rows = DoorEvents::whereDate( 'doordate', $dtLondon->toDateString())->get();

/*    	foreach ($employees as $value) {
    		$eventType = DoorEvents::whereDate( 'doordate', $dtLondon->toDateString())->where( 'empref',$value->empref)
				->latest('dooraccessref')->select('empref', 'doorevent')->first();
    		if (is_object($eventType)) {
				$whereabouts[] = [ 'name' => $value->name, 'event' => $eventType->eventtype ];
			} else {
				$whereabouts[] = [ 'name' => $value->name, 'event' => 'N/A' ];
			}
		} */

		return view('home', compact('reportDate', 'employees', 'rows'));
    }
}
