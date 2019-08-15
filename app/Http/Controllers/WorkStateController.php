<?php

namespace App\Http\Controllers;

use App\WorkState;
use Illuminate\Http\Request;

class WorkStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $work_states = WorkState::all();

        return view('workstates.index')->with(['work_states' => $work_states]);
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
            'work_state' => 'required',
        ]);
        WorkState::create($validatedData);

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
        $work_state = WorkState::findOrFail($id);

        return view('workstates.edit')->with(['work_state' => $work_state]);
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
            'work_state' => 'required',
        ]);
        WorkState::whereId($id)->update($validatedData);

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
        $work_state = WorkState::findOrFail($id);
        $work_state->delete();

        return redirect('/workstates')->with('success', 'Work state deleted');
    }
}
