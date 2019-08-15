<?php

namespace App\Http\Controllers;

use App\AbsenceType;
use Illuminate\Http\Request;

class AbsenceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $absenceLookup = AbsenceType::all();

        return view('absencetypes.index')->with(['abstypes' => $absenceLookup]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('absencetypes.create');
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
            'name' => 'required',
        ]);
        AbsenceType::create($validatedData);

        return redirect('/absencetypes')->with('success', 'Absence type saved');
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
        $absenceLookup = AbsenceType::findOrFail($id);

        return view('absencetypes.edit')->with(['absenceLookup' => $absenceLookup]);
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
            'name' => 'required',
        ]);
        AbsenceType::whereId($id)->update($validatedData);

        return redirect('/absencetypes')->with('success', 'Absence type updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @throws
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $absenceLookup = AbsenceType::findOrFail($id);
        $absenceLookup->delete();

        return redirect('/absencetypes')->with('success', 'Absence type deleted');
    }
}
