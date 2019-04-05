<?php

namespace App\Http\Controllers;

use App\ConfigDB;
use App\Holiday;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		// Get year start and end from config database
		$dtYearStart = new Carbon(ConfigDB::select('value')->where('name','holidays_start')->get()
			->pluck('value')->implode(''), 'Europe/London');
		$dtYearEnd = new Carbon(ConfigDB::select('value')->where('name','holidays_end')->get()
			->pluck('value')->implode(''), 'Europe/London');
		$sYear = $dtYearStart->format('Y') . '-' . $dtYearEnd->format('Y');

		// Get staff_id from the logged in user
		$staff = Staff::select('staff_id', 'holiday_entitlement')->where('name', Auth::user()->name)->get();
		$t_staffId = $staff->pluck('staff_id')->implode('');
		$iEntitlement = (int) $staff->pluck('holiday_entitlement')->implode('');

		$allHolidays = Holiday::where('staff_id', $t_staffId)->where('deleted','0')
			->whereBetween('start',[$dtYearStart->toDateString(), $dtYearEnd->toDateString()])->orderBy('start')->get();


		// Map() paid days used until we have the DB populated
		// Map() flag for "can be edited/deleted"
		$allHolidays->map(function ($holiday) {
			// For non-multiple day holidays, easier to take holiday_type and map it to a float
			switch ($holiday['holiday_type']) {
				case 'Half Day (AM)':
				case 'Half Day (PM)':
					$holiday['days_paid'] = 0.5;
					break;
				case 'Single Day':
					$holiday['days_paid'] = 1;
					break;
				case 'Multiple Days':
					$dt = new Carbon($holiday['start'], 'Europe/London');
					$holiday['days_paid'] = $dt->diffInWeekdays(new Carbon($holiday['end'], 'Europe/London'));
					break;
			}
			$holiday['days_unpaid'] = 0;
			$dt = new Carbon('now','Europe/London');
			($dt->diffInSeconds(new Carbon($holiday['start'], 'Europe/London'), false) <=0)
				? $holiday['enableTools'] = false : $holiday['enableTools'] = true;
			return $holiday;
		});

		return view('holidays.index', compact('sYear', 'iEntitlement', 'allHolidays'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        //
    }
}
