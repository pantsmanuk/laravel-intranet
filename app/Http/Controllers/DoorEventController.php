<?php

namespace App\Http\Controllers;

use App\DoorEvent;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class DoorEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $door_events = DoorEvent::orderBy('created_at', 'desc')->get();
        $door_events->map(function ($event) {
            $event['name'] = User::whereId($event['user_id'])->pluck('name')->first();

            return $event;
        });

        return view('doorevents.index')->with(['doorevents' => $door_events]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff = User::whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('doorevents.create')->with(['staff' => $staff]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'created_at' => 'required',
        ]);
        $validatedData['event'] = (isset($request['event'])) ? 1 : 0;
        DoorEvent::create($validatedData);

        return redirect('/doorevents')->with('success', 'Door event saved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $door_event = DoorEvent::findOrFail($id);
        $staff = User::whereDate('deleted_at', '>=', Date::now('Europe/London')->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('doorevents.edit')->with(['doorevent' => $door_event, 'staff' => $staff]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'created_at' => 'required',
        ]);
        $validatedData['event'] = (isset($request['event'])) ? 1 : 0;
        DoorEvent::whereId($id)->update($validatedData);

        return redirect('/doorevents')->with('success', 'Door event updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $door_event = DoorEvent::findOrFail($id);
        $door_event->delete();

        return redirect('/doorevents')->with('success', 'Door event deleted');
    }
}
