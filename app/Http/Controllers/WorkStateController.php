<?php

namespace App\Http\Controllers;

use App\Workstate;
use Illuminate\Http\Request;

// @todo rename controller/class "WorkStateController"
class WorkstateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $work_states = Workstate::all();

        return view('workstates.index')->with(['workstates' => $work_states]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('workstates.create');
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
            'workstate' => 'required',
        ]);
        Workstate::create($validatedData);

        return redirect('/workstates')->with('success', 'Work state saved');
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
        $workstate = Workstate::findOrFail($id);

        return view('workstates.edit')->with(['workstate' => $workstate]);
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
            'workstate' => 'required',
        ]);
        Workstate::whereId($id)->update($validatedData);

        return redirect('/workstates')->with('success', 'Work state updated');
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
        $work_state = Workstate::findOrFail($id);
        $work_state->delete();

        return redirect('/workstates')->with('success', 'Work state deleted');
    }
}
