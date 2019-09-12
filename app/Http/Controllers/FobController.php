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
        $fob_names = [
            12 => '#1',
            13 => '#2',
            14 => '#3',
        ];

        $fobs = Fob::orderBy('created_at', 'DESC')
            ->paginate();
        $fobs->map(function ($fob) use ($fob_names) {
            $fob['fob_name'] = $fob_names[$fob['fob_id']];
            $fob['staff_name'] = User::where('id', $fob['user_id'])
                ->pluck('name')
                ->first();
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
        $fobs = [
            ['fob_id' => 12, 'name' => 'Spare fob #1'],
            ['fob_id' => 13, 'name' => 'Spare fob #2'],
            ['fob_id' => 14, 'name' => 'Spare fob #3'],
        ];
        $fobs = collect($fobs)->map(function ($fob) {
            return (object) $fob;
        })->whereNotIn('fob_id', Fob::whereDate('created_at', Date::now('Europe/London')
            ->toDateString())
            ->pluck('fob_id')
            ->toArray());

        // Get unassigned staff as collection
        $staff = User::select('id AS user_id', 'name')
            ->whereNotIn('id', Fob::where('created_at', 'LIKE', Date::now('Europe/London')->toDateString().'%')
                ->pluck('user_id')
                ->toArray())
            ->whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('name')
            ->get();

        return view('fobs.create')->with(['fobs' => $fobs, 'staff' => $staff]);
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
        $validatedData = $request->toArray();
        Fob::create($validatedData);

        return redirect('/fobs')->with('success', 'Fob assignment saved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Fob $fob
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Fob $fob)
    {
        $fobs = [
            ['fob_id' => 12, 'name' => 'Spare fob #1'],
            ['fob_id' => 13, 'name' => 'Spare fob #2'],
            ['fob_id' => 14, 'name' => 'Spare fob #3'],
        ];
        $fobs = collect($fobs)->map(function ($fob) {
            return (object) $fob;
        });

        $staff = User::select('id AS user_id', 'name')
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
     * @param \App\Fob                 $fob
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fob $fob)
    {
        $validatedData = $request->except(['_token', '_method']);
        Fob::whereId($fob->id)->update($validatedData);

        return redirect('/fobs')->with('success', 'Fob assignment updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Fob $fob
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fob $fob)
    {
        $fob->delete();

        return redirect('/fobs')->with('success', 'Fob assignment deleted');
    }
}
