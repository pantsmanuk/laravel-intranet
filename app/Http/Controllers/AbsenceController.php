<?php

namespace App\Http\Controllers;

use App\Absence;
use App\AbsenceLookup;
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
        $dtYearStart = Date::parse(Config::select('value')->where('name','holidays_start')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $dtYearEnd = Date::parse(Config::select('value')->where('name','holidays_end')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $sYear = $dtYearStart->format('Y') . '-' . $dtYearEnd->format('Y');

        $absences = Absence::select('absences.id', 'users.name AS user_name', 'absences.start_at', 'absences.end_at',
        'absence_lookup.name AS absence_type', 'absences.note', 'absences.days_paid', 'absences.days_unpaid',
        'absences.approved')
            ->join('users', 'absences.user_id', '=', 'users.id')
            ->join('absence_lookup', 'absences.absence_id', '=', 'absence_lookup.id')
            ->whereDate('start_at', '>=', $dtYearStart->format('Y-m-d H:i:s'))
            ->whereDate('end_at', '<=', $dtYearEnd->format('Y-m-d H:i:s'))
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

        $absences = AbsenceLookup::all();

        return view('absences.create')->with(['staff' => $staff, 'absences' => $absences]);
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
            'user_id' => 'required|numeric|between:1,25',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'absence_id' => 'required|numeric|between:1,11',
            'note' => 'string|max:80',
            'days_paid' => 'required|numeric',
            'days_unpaid' => 'required|numeric'
        ]);
        if (!isset($request->approved)) {
            $validatedData['approved']=false;
        } elseif ($request->approved == "on") {
            $validatedData['approved']=true;
        }
        $absence = Absence::create($validatedData);

        return redirect('\absences')->with('success', 'Absence saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function edit(Absence $absence)
    {
        $dtYearStart = Date::parse(Config::select('value')->where('name','holidays_start')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $dtYearEnd = Date::parse(Config::select('value')->where('name','holidays_end')->get()
            ->pluck('value')->implode(''), 'Europe/London');
        $staff = User::select('id AS empref', 'name')
            ->whereDate('deleted_at', '>=', $dtYearStart->toDateTimeString())
            ->whereDate('deleted_at', '<=', $dtYearEnd->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('name')
            ->get();

        $absences = AbsenceLookup::all();

        return view('absences.edit')->with([
            'absence' => $absence,
            'staff' => $staff,
            'absences' => $absences
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|numeric|between:1,25',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'absence_id' => 'required|numeric|between:1,11',
            'note' => 'string|max:80',
            'days_paid' => 'required|numeric',
            'days_unpaid' => 'required|numeric'
        ]);
        if (!isset($request->approved)) {
            $validatedData['approved']=false;
        } elseif ($request->approved == "on") {
            $validatedData['approved']=true;
        }
        Absence::whereId($absence->id)->update($validatedData);

        return redirect('\absences')->with('success', 'Absence updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Absence $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy($absence)
    {
        $absence->delete();

        return redirect('/absences')->with('success', 'Absence deleted');
    }
}
