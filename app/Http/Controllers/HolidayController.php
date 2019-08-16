<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Config;
use App\Mail\Action;
use App\Mail\Approval;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dtYearStart = Date::parse(Config::select('value')->where('name', 'holidays_start')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $dtYearEnd = Date::parse(Config::select('value')->where('name', 'holidays_end')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $sYear = $dtYearStart->format('Y').'-'.$dtYearEnd->format('Y');

        $absences = Absence::select('absences.id', 'started_at', 'ended_at', 'absence_id', 'absence_types.name AS absence_type', 'note', 'days_paid', 'days_unpaid', 'approved')
            ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
            ->where('user_id', '=', auth()->id())
            ->whereDate('started_at', '>=', $dtYearStart->format('Y-m-d H:i:s'))
            ->whereDate('ended_at', '<=', $dtYearEnd->format('Y-m-d H:i:s'))
            ->get();

        return view('holidays.index')->with(['sYear' => $sYear, 'absences' => $absences]);
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'started_at' => 'required',
            'start_type' => 'numeric|between:1,3',
            'ended_at'   => 'nullable',
            'end_type'   => 'numeric|between:1,2',
            'note'       => 'nullable|string|max:80',
        ]);

        $holidayData['user_id'] = auth()->id();

        if (is_null($validatedData['ended_at'])) {
            $validatedData['ended_at'] = $validatedData['started_at'];
        }
        switch ($validatedData['start_type']) {
            case 1:
            case 3:
                $holidayData['started_at'] = $validatedData['started_at'].' 09:00:00';
                break;
            case 2:
                $holidayData['started_at'] = $validatedData['started_at'].' 13:00:00';
        }
        switch ($validatedData['end_type']) {
            case 1:
                $holidayData['ended_at'] = $validatedData['ended_at'].' 13:00:00';
                break;
            case 2:
                $holidayData['ended_at'] = $validatedData['ended_at'].' 17:30:00';
        }

        $holidayData['absence_id'] = 1;
        $holidayData['note'] = $validatedData['note'];

        if ($validatedData['started_at'] === $validatedData['ended_at']) {
            $sDates = Date::parse($holidayData['started_at'], 'Europe/London')->format('j F');
            switch ($validatedData['start_type']) {
                case 1:
                    $holidayData['days_paid'] = 0.5;
                    $holidayType = 'Half Day (AM)';
                    break;
                case 2:
                    $holidayData['days_paid'] = 0.5;
                    $holidayType = 'Half Day (PM)';
                    break;
                case 3:
                    $holidayData['days_paid'] = 1.0;
                    $holidayType = 'Single Day';
            }
        } else {
            $holidayData['days_paid'] = Date::parse($holidayData['started_at'], 'Europe/London')
                ->diffInWeekdays(Date::parse($holidayData['ended_at'], 'Europe/London'));
            $sDates = Date::parse($holidayData['started_at'], 'Europe/London')->format('j F')
                .' to '.Date::parse($holidayData['ended_at'], 'Europe/London')->format('j F');
            $holidayType = 'Multiple Days';
        }
        $holidayData['days_unpaid'] = 0.0;
        $holidayData['approved'] = 0;

        $cOverlaps = Absence::overlaps($holidayData);
        $holiday = Absence::create($holidayData);

        $aRequest = [
            'holiday_dates'    => $sDates,
            'holiday_id'       => $holiday->id,
            'holiday_note'     => $holiday->note,
            'holiday_overlaps' => $cOverlaps,
            'holiday_type'     => $holidayType,
            'user_name'        => Auth::user()->name,
        ];

        Mail::to(Config::where('name', '=', 'email_approval')->pluck('value')->first())->send(new Approval($aRequest));

        return redirect('/holidays')->with('success', 'Holiday requested');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $holiday = Absence::findOrFail($id);
        if ($holiday->user_id == auth()->id()) {
            $request['started_at'] = substr($holiday['started_at'], 0, 10);
            $request['ended_at'] = substr($holiday['ended_at'], 0, 10);
            if ($request['started_at'] == $request['ended_at']) {
                if (substr($holiday->end_at, -8) == '13:00:00') {
                    $request['start_type'] = 1;
                    $request['end_type'] = 1;
                } elseif (substr($holiday->started_at, -8) == '13:00:00') {
                    $request['start_type'] = 2;
                    $request['end_type'] = 2;
                } else {
                    $request['start_type'] = 3;
                    $request['end_type'] = 2;
                }
            } else {
                if (substr($holiday->started_at, -8) == '09:00:00') {
                    $request['start_type'] = 3;
                } else {
                    $request['start_type'] = 2;
                }
                if (substr($holiday->ended_at, -8) == '13:00:00') {
                    $request['end_type'] = 1;
                } else {
                    $request['end_type'] = 2;
                }
            }

            return view('holidays.edit')->with(['holiday' => $holiday, 'request' => $request]);
        } else {
            return redirect('/holidays')->with('errors', 'Cannot edit other users holiday requests');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'started_at' => 'required',
            'start_type' => 'numeric|between:1,3',
            'ended_at'   => 'nullable',
            'end_type'   => 'numeric|between:1,2',
            'note'       => 'nullable|string|max:80',
        ]);

        $holidayData['user_id'] = auth()->id();

        if (is_null($validatedData['ended_at'])) {
            $validatedData['ended_at'] = $validatedData['started_at'];
        }
        switch ($validatedData['start_type']) {
            case 1:
            case 3:
                $holidayData['started_at'] = $validatedData['started_at'].' 09:00:00';
                break;
            case 2:
                $holidayData['started_at'] = $validatedData['started_at'].' 13:00:00';
        }
        switch ($validatedData['end_type']) {
            case 1:
                $holidayData['ended_at'] = $validatedData['ended_at'].' 13:00:00';
                break;
            case 2:
                $holidayData['ended_at'] = $validatedData['ended_at'].' 17:30:00';
        }

        $holidayData['absence_id'] = 1;
        $holidayData['note'] = $validatedData['note'];

        if ($validatedData['started_at'] === $validatedData['ended_at']) {
            $sDates = Date::parse($holidayData['started_at'], 'Europe/London')->format('j F');
            switch ($validatedData['start_type']) {
                case 1:
                    $holidayData['days_paid'] = 0.5;
                    $holidayType = 'Half Day (AM)';
                    break;
                case 2:
                    $holidayData['days_paid'] = 0.5;
                    $holidayType = 'Half Day (PM)';
                    break;
                case 3:
                    $holidayData['days_paid'] = 1.0;
                    $holidayType = 'Single Day';
            }
        } else {
            $holidayData['days_paid'] = Date::parse($holidayData['started_at'], 'Europe/London')
                ->diffInWeekdays(Date::parse($holidayData['ended_at'], 'Europe/London'));
            $sDates = Date::parse($holidayData['started_at'], 'Europe/London')->format('j F')
                .' to '.Date::parse($holidayData['ended_at'], 'Europe/London')->format('j F');
            $holidayType = 'Multiple Days';
        }
        $holidayData['days_unpaid'] = 0.0;
        $holidayData['approved'] = 0;

        $cOverlaps = Absence::overlaps($holidayData);
        Absence::where('id', $id)->update($holidayData);

        $aRequest = [
            'holiday_dates'    => $sDates,
            'holiday_id'       => $id,
            'holiday_note'     => $holidayData['note'],
            'holiday_overlaps' => $cOverlaps,
            'holiday_type'     => $holidayType,
            'user_name'        => Auth::user()->name,
        ];

        Mail::to(Config::where('name', '=', 'email_approval')->pluck('value')->first())
            ->send(new Approval($aRequest));

        return redirect('/holidays')->with('success', 'Holiday request updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Absence::findOrFail($id);
        if ($holiday->user_id == auth()->id()) {
            $holiday->delete();

            $aRequest['holiday_action'] = 'Cancelled';
            if (substr($holiday->started_at, 0, 10) == substr($holiday->ended_at, 0, 10)) {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F');
                if (substr($holiday->ended_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (AM)';
                } elseif (substr($holiday->started_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (PM)';
                } else {
                    $aRequest['holiday_type'] = 'Single Day';
                }
            } else {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F')
                    .' to '.Date::parse($holiday->ended_at, 'Europe/London')->format('j F');
                $aRequest['holiday_type'] = 'Multiple Days';
            }
            $aRequest['user_name'] = Auth::user()->name;

            Mail::to(Auth::user()->email)
                ->cc(Config::where('name', '=', 'email_notification')->pluck('value')->first())
                ->send(new Action($aRequest));

            return redirect('/holidays')->with('success', 'Holiday request deleted');
        } else {
            return redirect('/holidays')->with('errors', 'Cannot delete other users holiday requests');
        }
    }

    /**
     * Approve the specified resource in storage.
     *
     * @param string $secret
     *
     * @return \Illuminate\Http\Response
     */
    public function approve($secret)
    {
        $aData = explode(' ', Crypt::decryptString($secret));
        $id = $aData[0];
        $uuid = $aData[1];

        $holiday = Absence::findOrFail($id);
        $stored_uuid = DB::table('uuid')->where('id', '=', $id)->pluck('id_text')->first();

        if ($stored_uuid === strtoupper($uuid)) {
            $holiday->update(['approved' => 1]);
            DB::table('uuid')->where('id', '=', $id)->delete();

            $aRequest['holiday_action'] = 'Approved';
            if (substr($holiday->started_at, 0, 10) == substr($holiday->ended_at, 0, 10)) {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F');
                if (substr($holiday->ended_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (AM)';
                } elseif (substr($holiday->started_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (PM)';
                } else {
                    $aRequest['holiday_type'] = 'Single Day';
                }
            } else {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F')
                    .' to '.Date::parse($holiday->ended_at, 'Europe/London')->format('j F');
                $aRequest['holiday_type'] = 'Multiple Days';
            }
            $aRequest['user_name'] = User::whereId($holiday->user_id)->pluck('name')->first();

            Mail::to(User::whereId($holiday->user_id)->pluck('email')->first())
                ->cc(Config::where('name', '=', 'email_notification')->pluck('value')->first())
                ->send(new Action($aRequest));

            return redirect('/holidays')->with('success', 'Holiday request approved');
        } else {
            return redirect('/holidays')->with('error', 'Approval error');
        }
    }

    /**
     * Deny the specified resource in storage.
     *
     * @param string $secret
     *
     * @return \Illuminate\Http\Response
     */
    public function deny($secret)
    {
        $aData = explode(' ', Crypt::decryptString($secret));
        $id = $aData[0];
        $uuid = $aData[1];

        $holiday = Absence::findOrFail($id);
        $stored_uuid = DB::table('uuid')->where('id', '=', $id)->pluck('id_text')->first();

        if ($stored_uuid === strtoupper($uuid)) {
            $holiday->delete();
            DB::table('uuid')->where('id', '=', $id)->delete();

            $aRequest['holiday_action'] = 'Denied';
            if (substr($holiday->started_at, 0, 10) == substr($holiday->ended_at, 0, 10)) {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F');
                if (substr($holiday->ended_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (AM)';
                } elseif (substr($holiday->started_at, -8) == '13:00:00') {
                    $aRequest['holiday_type'] = 'Half Day (PM)';
                } else {
                    $aRequest['holiday_type'] = 'Single Day';
                }
            } else {
                $aRequest['holiday_dates'] = Date::parse($holiday->started_at, 'Europe/London')->format('j F')
                    .' to '.Date::parse($holiday->ended_at, 'Europe/London')->format('j F');
                $aRequest['holiday_type'] = 'Multiple Days';
            }
            $aRequest['user_name'] = User::whereId($holiday->user_id)->pluck('name')->first();

            Mail::to(User::whereId($holiday->user_id)->pluck('email')->first())
                ->cc(Config::where('name', '=', 'email_notification')->pluck('value')->first())
                ->send(new Action($aRequest));

            return redirect('/holidays')->with('success', 'Holiday request denied');
        } else {
            return redirect('/holidays')->with('error', 'Denial error');
        }
    }
}
