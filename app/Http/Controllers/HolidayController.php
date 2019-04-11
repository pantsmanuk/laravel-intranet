<?php

namespace App\Http\Controllers;

use App\ConfigDB;
use App\Holiday;
use App\Mail\Action;
use App\Mail\Approval;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Class HolidayController
 *
 * @package App\Http\Controllers
 * @todo "Nonce model" as part of the approval workflow?
 * @todo Might not need Approval/Approve/Deny handling?
 */
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
			if (is_null($holiday['days_paid'])) {
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
			}
			if (is_null($holiday['days_unpaid'])) {
				$holiday['days_unpaid']=0;
			}
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

    	// endType sometimes not returned, default to 2 (Full Day)
    	if(!isset($validatedData['endType'])) {
    		$validatedData['endType'] = 2;
		}

		// Staff::get intranet staff_id based on something from Laravel $user
		$staff = Staff::select('staff_id')->where('name', Auth::user()->name)->get();
		$holidayRequest['staff_id'] = (int) $staff->pluck('staff_id')->implode('');

		// start and end to datetime strings
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

		// startType and endType to enum values
		if($validatedData['start']===$validatedData['end']) {
			if(!is_null($validatedData['startType'])) {
				switch ($validatedData['startType']) {
					case 1:
						$holidayRequest['holiday_type'] = "Half Day (AM)";
						$validatedData['endType'] = 1;
						$holidayRequest['days_paid'] = 0.5;
						$t_Dates = $holidayRequest['start'];
						break;
					case 2:
						$holidayRequest['holiday_type'] = "Half Day (PM)";
						$validatedData['endType'] = 2;
						$holidayRequest['days_paid'] = 0.5;
						$t_Dates = $holidayRequest['start'];
						break;
					case 3:
						$holidayRequest['holiday_type'] = "Single Day";
						$validatedData['endType'] = 2;
						$holidayRequest['days_paid'] = 1;
						$t_Dates = $holidayRequest['start'];
						break;
				}
			}
		} else {
			$holidayRequest['holiday_type'] = "Multiple Days";
			$dt = new Carbon($holidayRequest['start'], 'Europe/London');
			$holidayRequest['days_paid'] = $dt->diffInWeekdays(new Carbon($holidayRequest['end'], 'Europe/London'));
			$t_Dates = $holidayRequest['start'].' to '.$holidayRequest['end'];
		}

		// absence_id
		$holidayRequest['absence_id'] = 1;

		// note cannot be null
		if(isset($validatedData['note'])) {
			$holidayRequest['note'] = $validatedData['note'];
		} else {
			$holidayRequest['note'] = '';
		}

		// days_unpaid
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

		// Throw the request at the DB table, see what sticks
    	$holiday = Holiday::create($holidayRequest);

		// Send approval email
		$t_Start = substr($holiday->start,0,10);
		$t_End = substr($holiday->end,0,10);
		$t_Overlaps = DB::connection('mysql')
			->select('SELECT staff.name, holidays.start, holidays.end, holidays.approved
			FROM holidays
			JOIN staff ON holidays.staff_id=staff.staff_id
			WHERE holidays.holiday_id!=?
			AND (holidays.deleted_at IS NULL OR holidays.deleted=0)
			AND (holidays.start BETWEEN ? AND ? 
			     OR holidays.end BETWEEN ? AND ?
			     OR (holidays.start<? AND holidays.end>?))',
			[$holiday->holiday_id, $t_Start, $t_End, $t_Start, $t_End, $t_Start, $t_End]);
		$t_Request = [
			'holiday_dates'=>$t_Dates,
			'holiday_id'=>$holiday->holiday_id,
			'holiday_nonce'=>'holiday_nonce', // Might not need nonces, would make the logic far simpler, would make security lower...
			'holiday_note'=>$holidayRequest['note'],
			'holiday_overlaps'=>$t_Overlaps,
			'holiday_type'=>$holidayRequest['holiday_type'],
			'user_name'=>Auth::user()->name,
			];

		Mail::to('murray.crane@gmail.com')
			->send(new Approval($t_Request));

    	return redirect('/holidays')->with('success', 'Holiday requested');
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

		// start, startType, end and endType
		$tStart = substr($holiday['start'],-8);
		$tEnd = substr($holiday['end'],-8);
		$request['start'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['start'], 'Europe/London')->format('d/m/Y');
		$request['end'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['end'], 'Europe/London')->format('d/m/Y');
		switch($holiday['holiday_type']) {
			case 'Half Day (AM)':
				$request['startType']=1;
				$request['endType']=1;
				break;
			case 'Half Day (PM)':
				$request['startType']=2;
				$request['endType']=2;
				break;
			case 'Single Day':
				$request['startType']=3;
				$request['endType']=2;
				break;
			case 'Multiple Days':
				if($tStart=='09:00:00') {
					$request['startType']=3;
				} elseif($tStart=='13:00:00') {
					$request['startType']=2;
				}
				if($tEnd=='13:00:00') {
					$request['endType']=1;
				} elseif($tEnd=='17:30:00') {
					$request['endType']=2;
				}
				break;
		}


        return view('holidays.edit', compact('holiday', 'request'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$validatedData = $request->validate([
			'start' => 'required|date_format:d/m/Y',
			'startType' => 'numeric|between:1,3',
			'end' => 'date_format:d/m/Y',
			'endType' => 'numeric|between:1,2',
			'note' => 'nullable|string|max:80',
		]);

		// endType sometimes not returned, default to 2 (Full Day)
		if(!isset($validatedData['endType'])) {
			$validatedData['endType'] = 2;
		}

		// Staff::get intranet staff_id based on something from Laravel $user
		$staff = Staff::select('staff_id')->where('name', Auth::user()->name)->get();
		$holidayRequest['staff_id'] = (int) $staff->pluck('staff_id')->implode('');

		// start and end to datetime strings
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

		// startType and endType to enum values
		if($validatedData['start']===$validatedData['end']) {
			if(!is_null($validatedData['startType'])) {
				switch ($validatedData['startType']) {
					case 1:
						$holidayRequest['holiday_type'] = "Half Day (AM)";
						$validatedData['endType'] = 1;
						$holidayRequest['days_paid'] = 0.5;
						$t_Dates = $holidayRequest['start'];
						break;
					case 2:
						$holidayRequest['holiday_type'] = "Half Day (PM)";
						$validatedData['endType'] = 2;
						$holidayRequest['days_paid'] = 0.5;
						$t_Dates = $holidayRequest['start'];
						break;
					case 3:
						$holidayRequest['holiday_type'] = "Single Day";
						$validatedData['endType'] = 2;
						$holidayRequest['days_paid'] = 1;
						$t_Dates = $holidayRequest['start'];
						break;
				}
			}
		} else {
			$holidayRequest['holiday_type'] = "Multiple Days";
			$dt = new Carbon($holidayRequest['start'], 'Europe/London');
			$holidayRequest['days_paid'] = $dt->diffInWeekdays(new Carbon($holidayRequest['end'], 'Europe/London'));
			$t_Dates = $holidayRequest['start'].' to '.$holidayRequest['end'];
		}

		// absence_id
		$holidayRequest['absence_id'] = 1;

		// note cannot be null
		if(isset($validatedData['note'])) {
			$holidayRequest['note'] = $validatedData['note'];
		} else {
			$holidayRequest['note'] = '';
		}

		// days_unpaid
		$holidayRequest['days_unpaid'] = 0;

		// approved
		$holidayRequest['confirmed'] = 1; // @KLUDGE: Until the CI intranet is retired
		$holidayRequest['approved'] = 0;

		// nonce
		$holidayRequest['deleted'] = 0; // @KLUDGE: Until the CI intranet is retired
		$holidayRequest['nonce'] = 0;

		// machine_id - drop the last 10 chars for GGP
		$holidayRequest['machine_id'] = substr(gethostbyaddr(request()->ip()),0,strlen(gethostbyaddr(request()->ip()))-10).' ('.request()->ip().')'; // REMOTE_ADDR from the server?

		// Other column I don't care about - updated
		// created_at, updated_at, deleted_at are "Laravel protected"
		//dd($holidayRequest);

		// Throw the request at the DB table, see what sticks
		Holiday::where('holiday_id',$id)->update($holidayRequest);

		// Send approval email
		$t_Start = substr($holidayRequest['start'],0,10);
		$t_End = substr($holidayRequest['end'],0,10);
		$t_Overlaps = DB::connection('mysql')
			->select('SELECT staff.name, holidays.start, holidays.end, holidays.approved
			FROM holidays
			JOIN staff ON holidays.staff_id=staff.staff_id
			WHERE holidays.holiday_id!=?
			AND (holidays.deleted_at IS NULL OR holidays.deleted=0)
			AND (holidays.start BETWEEN ? AND ? 
			     OR holidays.end BETWEEN ? AND ?
			     OR (holidays.start<? AND holidays.end>?))',
				[$id, $t_Start, $t_End, $t_Start, $t_End, $t_Start, $t_End]);
		$t_Request = [
			'holiday_dates'=>$t_Dates,
			'holiday_id'=>$id,
			'holiday_nonce'=>'holiday_nonce',
			'holiday_note'=>$holidayRequest['note'],
			'holiday_overlaps'=>$t_Overlaps,
			'holiday_type'=>$holidayRequest['holiday_type'],
			'user_name'=>Auth::user()->name,
		];

		Mail::to('murray.crane@ggpsystems.co.uk')
			->send(new Approval($t_Request));

		return redirect('/holidays')->with('success', 'Holiday request updated');
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
        $holiday->where('holiday_id', $id)->update(['deleted'=>1]); // @KLUDGE: Until the CI intranet is retired
        $holiday->delete();

		// Send cancelled email
		$t_Start = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['start'], 'Europe/London')->format('d/m/Y H:i:s');
		$t_End = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['end'], 'Europe/London')->format('d/m/Y H:i:s');
		switch ($holiday['holiday_type']) {
			case 'Half Day (AM)':
			case 'Half Day (PM)':
			case 'Single Day':
				$t_Dates = substr($t_Start,0,10);
				break;
			case 'Multiple Days':
				$t_Dates = substr($t_Start,0,10).' to '.substr($t_End,0,10);
				break;
		}
		$t_Request = [
			'holiday_action'=>'Cancelled',
			'holiday_dates'=>$t_Dates,
			'holiday_type'=>$holiday['holiday_type'],
			'user_name'=>Auth::user()->name,
		];

		Mail::to(Auth::user()->email)
			->cc('holidays@ggpsystems.co.uk')
			->send(new Action($t_Request));

		return redirect('/holidays')->with('success', 'Holiday request deleted');
    }

	/**
	 * Approve the specified resource in storage.
	 *
	 * @param integer	$id
	 * @return \Illuminate\Http\Response
	 */
	public function approve($id)
	{
		$holiday = Holiday::findOrFail($id);
		$holiday->where('holiday_id',$id)->update(['approved'=>1]);

		// Send approved email
		$t_Start = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['start'], 'Europe/London')->format('d/m/Y H:i:s');
		$t_End = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['end'], 'Europe/London')->format('d/m/Y H:i:s');
		switch ($holiday['holiday_type']) {
			case 'Half Day (AM)':
			case 'Half Day (PM)':
			case 'Single Day':
				$t_Dates = substr($t_Start,0,10);
				break;
			case 'Multiple Days':
				$t_Dates = substr($t_Start,0,10).' to '.substr($t_End,0,10);
				break;
		}
		$staff = Staff::findOrFail($holiday['staff_id']);
		$t_Request = [
			'holiday_action'=>'Approved',
			'holiday_dates'=>$t_Dates,
			'holiday_type'=>$holiday['holiday_type'],
			'user_name'=>$staff['name'],
		];

		Mail::to($staff['email'])
			->cc('holidays@ggpsystems.co.uk')
			->send(new Action($t_Request));

		return redirect('/holidays')->with('success', 'Holiday request approved');
	}

	/**
	 * Deny the specified resource in storage.
	 *
	 * @param integer	$id
	 * @return \Illuminate\Http\Response
	 */
	public function deny($id)
	{
		// Like delete, but with an updated note??
		// Needs to get and check `nonce` if we continue using it
		$holiday = Holiday::findOrFail($id);
		$holiday->where('holiday_id', $id)->update(['deleted'=>1]); // @KLUDGE: Until the CI intranet is retired
		$holiday->delete();

		// @TODO: approved email
		$t_Start = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['start'], 'Europe/London')->format('d/m/Y H:i:s');
		$t_End = Carbon::createFromFormat('Y-m-d H:i:s', $holiday['end'], 'Europe/London')->format('d/m/Y H:i:s');
		switch ($holiday['holiday_type']) {
			case 'Half Day (AM)':
			case 'Half Day (PM)':
			case 'Single Day':
				$t_Dates = substr($t_Start,0,10);
				break;
			case 'Multiple Days':
				$t_Dates = substr($t_Start,0,10).' to '.substr($t_End,0,10);
				break;
		}
		$staff = Staff::findOrFail($holiday['staff_id']);
		$t_Request = [
			'holiday_action'=>'Denied',
			'holiday_dates'=>$t_Dates,
			'holiday_type'=>$holiday['holiday_type'],
			'user_name'=>$staff['name'],
		];

		Mail::to($staff['email'])
			->cc('holidays@ggpsystems.co.uk')
			->send(new Action($t_Request));

		return redirect('/holidays')->with('success', 'Holiday request denied');
	}
}
