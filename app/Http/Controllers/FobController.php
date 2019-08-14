<?php

namespace App\Http\Controllers;

use App\Fob;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class FobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fob_names = array(
            12 => '#1',
            13 => '#2',
            14 => '#3'
        );

        $fobs = Fob::whereNull('deleted_at')->orderBy('created_at', 'DESC')->get();
        $fobs->map(function ($fob) use ($fob_names) {
            $fob['fob_name'] = $fob_names[$fob['FobID']];
            $fob['staff_name'] = User::where('id', $fob['UserID'])
                ->pluck('name')->first();
        });

        return view('fobs.index')->with('fobs', $fobs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get unused spare fobs as collection
        $fobs = array(['FobID' => 12, 'name' => 'Spare fob #1'],
            ['FobID' => 13, 'name' => 'Spare fob #2'],
            ['FobID' => 14, 'name' => 'Spare fob #3']);
        $fobs = collect($fobs)->map(function ($fob) {
            return (object)$fob;
        })->whereNotIn('FobID', Fob::whereDate('created_at', Date::now('Europe/London')
            ->toDateString())
            ->pluck('FobID')
            ->toArray());

        // Get unassigned staff as collection
        $staff = User::select('id AS UserID', 'name')
            ->whereNotIn('id', Fob::where('created_at', 'LIKE', Date::now('Europe/London')->toDateString() . "%")
                ->pluck('UserID')
                ->toArray())
            ->whereRaw('(deleted_at >= "' . Date::now('Europe/London')->toDateTimeString() . '" OR deleted_at IS NULL)')
            ->orderByRaw('name')
            ->get();

        return view('fobs.create')->with(['fobs' => $fobs, 'staff' => $staff]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->toArray();
        $validatedData['MachineID'] = "(" . $request->ip() . ")" . auth()->user()->name;
        $validatedData['date'] = Date::now('Europe/London');
        Fob::create($validatedData);

        return redirect('/fobs')->with('success', 'Fob assignment saved');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Fob $fob
     * @return \Illuminate\Http\Response
     */
    public function show(Fob $fob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Fob $fob
     * @return \Illuminate\Http\Response
     */
    public function edit(Fob $fob)
    {
        $fobs = array(['FobID' => 12, 'name' => 'Spare fob #1'],
            ['FobID' => 13, 'name' => 'Spare fob #2'],
            ['FobID' => 14, 'name' => 'Spare fob #3']);
        $fobs = collect($fobs)->map(function ($fob) {
            return (object)$fob;
        });

        $staff = User::select('id AS UserID', 'name')
            ->whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('name')
            ->get();

        return view('fobs.edit')->with(['fob' => $fob, 'fobs' => $fobs, 'staff' => $staff]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Fob $fob
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fob $fob)
    {
        $validatedData = $request->except(['_token', '_method']);
        $validatedData['MachineID'] = "(" . $request->ip() . ")" . auth()->user()->name;
        Fob::whereId($fob->id)->update($validatedData);
        return redirect('/fobs')->with('success', 'Fob assignment updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Fob $fob
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fob $fob)
    {
        $fob->delete();
        return redirect('/fobs')->with('success', 'Fob assignment deleted');
    }
}
