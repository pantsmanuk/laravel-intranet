<?php

namespace App\Http\Controllers;

use App\Absence;
use App\AbsenceType;
use App\Config;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class AbsenceController extends Controller
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

        $absences = Absence::select('absences.id', 'users.name AS user_name', 'started_at', 'ended_at', 'absence_id',
            'absence_types.name AS absence_type', 'note', 'days_paid', 'days_unpaid', 'approved')
            ->join('users', 'absences.user_id', '=', 'users.id')
            ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
            ->whereDate('started_at', '>=', $dtYearStart->format('Y-m-d H:i:s'))
            ->whereDate('ended_at', '<=', $dtYearEnd->format('Y-m-d H:i:s'))
            ->get();

        return view('absences.index')->with(['sYear' => $sYear, 'absences' => $absences]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff = User::select('id AS empref', 'name')
            ->whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('name')
            ->get();

        $absences = AbsenceType::all();

        return view('absences.create')->with(['staff' => $staff, 'absences' => $absences]);
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
            'user_id'     => 'required|numeric|between:1,25',
            'started_at'  => 'required|date',
            'ended_at'    => 'required|date',
            'absence_id'  => 'required|numeric|between:1,11',
            'note'        => 'string|max:80',
            'days_paid'   => 'required|numeric',
            'days_unpaid' => 'required|numeric',
        ]);
        if (!isset($request->approved)) {
            $validatedData['approved'] = false;
        } elseif ($request->approved == 'on') {
            $validatedData['approved'] = true;
        }
        Absence::create($validatedData);

        return redirect('/absences')->with('success', 'Absence saved');
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
        $absence = Absence::findOrFail($id);

        $dtYearStart = Date::parse(Config::select('value')->where('name', 'holidays_start')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $dtYearEnd = Date::parse(Config::select('value')->where('name', 'holidays_end')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $staff = User::select('id AS empref', 'name')
            ->whereDate('deleted_at', '>=', $dtYearStart->toDateTimeString())
            ->whereDate('deleted_at', '<=', $dtYearEnd->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('name')
            ->get();

        $absences = AbsenceType::all();

        return view('absences.edit')->with([
            'absence'  => $absence,
            'staff'    => $staff,
            'absences' => $absences,
        ]);
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
            'user_id'     => 'required|numeric|between:1,25',
            'started_at'  => 'required|date',
            'ended_at'    => 'required|date',
            'absence_id'  => 'required|numeric|between:1,11',
            'note'        => 'string|max:80',
            'days_paid'   => 'required|numeric',
            'days_unpaid' => 'required|numeric',
        ]);
        if (!isset($request->approved)) {
            $validatedData['approved'] = false;
        } elseif ($request->approved == 'on') {
            $validatedData['approved'] = true;
        }
        Absence::whereId($id)->update($validatedData);

        return redirect('/absences')->with('success', 'Absence updated');
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
        $absence = Absence::findOrFail($id);
        $absence->delete();

        return redirect('/absences')->with('success', 'Absence deleted');
    }
}
