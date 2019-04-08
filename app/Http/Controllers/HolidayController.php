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
		$t_staffId = (int) $staff->pluck('staff_id')->implode('');
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
    	$validatedData = $request->validate([
    		'start' => 'required|date_format:d/m/Y',
			'startType' => 'numeric|between:1,3',
			'end' => 'date_format:d/m/Y',
			'endType' => 'numeric|between:1,2',
			'note' => 'nullable|string|max:80',
		]);

    	if(!isset($validatedData['endType'])) {
    		$validatedData['endType'] = 2; // Default to "Full Day"
		}

    	// @TODO we have a good request, munge it and throw it at the DB table
		// Staff::get intranet staff_id based on something from Laravel $user
		$staff = Staff::select('staff_id')->where('name', Auth::user()->name)->get();
		$holidayRequest['staff_id'] = (int) $staff->pluck('staff_id')->implode('');

		// Turn start and end into datetime strings
		if(is_null($validatedData['end'])) {
			$validatedData['end'] = $validatedData['start'];
		}
		if(!is_null($validatedData['startType'])) {
			switch ($validatedData['startType']) {
				case 1:
				case 3:
					$validatedData['start'].=" 09:00:00";
					break;
				case 2:
					$validatedData['start'].=" 13:00:00";
					break;
			}
		}
		if(!is_null($validatedData['endType'])) {
			switch ($validatedData['endType']) {
				case 1:
					$validatedData['end'].=" 13:00:00";
					break;
				case 2:
					$validatedData['end'].=" 17:30:00";
					break;
			}
		}
		$holidayRequest['start'] = Carbon::createFromFormat('d/m/Y H:i:s', $validatedData['start'], 'Europe/London')->format('Y-m-d H:i:s');
		$holidayRequest['end'] = Carbon::createFromFormat('d/m/Y H:i:s', $validatedData['end'], 'Europe/London')->format('Y-m-d H:i:s');

		// Turn startType and endType into enum values
		if($validatedData['start']===$validatedData['end']) {
			if(!is_null($validatedData['startType'])) {
				switch ($validatedData['startType']) {
					case 1:
						$holidayRequest['holiday_type'] = "Half Day (AM)";
						$validatedData['endType'] = 1;
						break;
					case 2:
						$holidayRequest['holiday_type'] = "Half Day (PM)";
						$validatedData['endType'] = 2;
						break;
					case 3:
						$holidayRequest['holiday_type'] = "Single Day";
						$validatedData['endType'] = 2;
						break;
				}
			}
		} else {
			$holidayRequest['holiday_type'] = "Multiple Days";
		}

		// note cannot be null
		if(isset($validatedData['note'])) {
			$holidayRequest['note'] = $validatedData['note'];
		} else {
			$holidayRequest['note'] = '';
		}

		// days_paid/days_unpaid
		$holidayRequest['days_paid'] = 0;
		$holidayRequest['days_unpaid'] = 0;

		// approved
		$holidayRequest['confirmed'] = 1; // I don't care
		$holidayRequest['approved'] = 0;

		// nonce
		$holidayRequest['deleted'] = 0; // I don't care
		$holidayRequest['nonce'] = 0;

		// machine_id - drop the last 10 chars for GGP
		$holidayRequest['machine_id'] = substr(gethostbyaddr(request()->ip()),0,strlen(gethostbyaddr(request()->ip()))-10).' ('.request()->ip().')'; // REMOTE_ADDR from the server?

		// Other column I don't care about - updated
		// created_at, updated_at, deleted_at are "Laravel protected"
		//dd($holidayRequest);

		// Throw the request at the DB table, see what sticks
    	$holiday = Holiday::create($holidayRequest);

    	// Send the email for acceptance

    	return redirect('/holidays')->with('success', 'Holiday requested');
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
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);

        return view('holidays.edit', compact('request'));
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
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect('/holidays')->with('success', 'Holiday request deleted');
    }
}
