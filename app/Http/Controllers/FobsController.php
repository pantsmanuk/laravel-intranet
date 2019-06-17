<?php

namespace App\Http\Controllers;

use App\Fob;
use App\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class FobsController extends Controller
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

        $fobs = Fob::all();
        $fobs->map(function ($fob) use ($fob_names){
            $fob['fob_name'] = $fob_names[$fob['empref']];
            $fob['staff_name'] = Staff::where('staff_id',$fob['staff_id'])
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
        $fobs = array(['empref' => 12, 'name' => 'Spare fob #1'],
            ['empref' => 13, 'name' => 'Spare fob #2'],
            ['empref' => 14, 'name' => 'Spare fob #3']);
        $fobs = collect($fobs)->map(function ($fob) {
            return (object) $fob;
        })->whereNotIn('empref', Fob::whereDate('created_at', Date::now('Europe/London')
            ->toDateString())
            ->pluck('empref')
            ->toArray());

        // Get unassigned staff as collection
        $staff = Staff::select('staff_id', 'name')
            ->whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('firstname, surname')
            ->get()
            ->whereNotIn('staff_id', Fob::whereDate('created_at', Date::now('Europe/London')
                ->toDateString())
                ->pluck('staff_id')
                ->toArray());

        return view('fobs.create')->with(['fobs' => $fobs, 'staff' => $staff]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->toArray();
        $fob = Fob::create($validatedData);

        return redirect('/fobs')->with('success', 'Fob assignment saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fob  $fob
     * @return \Illuminate\Http\Response
     */
    public function show(Fob $fob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Fob  $fob
     * @return \Illuminate\Http\Response
     */
    public function edit(Fob $fob)
    {
        // @todo Unused spare fobs as collection
        $fobs = array(['empref' => 12, 'name' => 'Spare fob #1'],
            ['empref' => 13, 'name' => 'Spare fob #2'],
            ['empref' => 14, 'name' => 'Spare fob #3']);
        $fobs = collect($fobs)->map(function ($fob) {
            return (object) $fob;
        });
        // @todo Get unassigned staff as collection
        $staff = Staff::select('staff_id', 'name')
            ->whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('firstname, surname')
            ->get();

        return view('fobs.edit')->with(['fob' => $fob, 'fobs' => $fobs, 'staff' => $staff]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fob  $fob
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fob $fob)
    {
        $validatedData = $request->except(['_token', '_method'])->toArray();
        Fob::whereId($fob->id)->update($validatedData);
        return redirect('/fobs')->with('success', 'Fob assignment updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fob  $fob
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fob $fob)
    {
        $fob->delete();
        return redirect('/fobs')->with('success', 'Fob assignment deleted');
    }
}
