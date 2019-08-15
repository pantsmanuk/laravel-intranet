<?php

namespace App\Http\Controllers;

use App\Telephone;
use App\UserTelephone;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class TelephoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Do a map to add in the user name from user_id, or flip this on it's head and base everything off
        // UserTelephoneLookup in the first instance?
        $telephones = UserTelephone::select('users_telephones.id AS lookup_id', 'user_id', 'telephone_id',
            'u.name AS user_name', 't.name', 't.number')
            ->join('telephones AS t', 'users_telephones.telephone_id', '=', 't.id')
            ->join('users AS u', 'users_telephones.user_id', '=', 'u.id')
            ->orderByRaw('u.name, t.id ASC')
            ->get();

        return view('telephones.index')->with(['telephones' => $telephones]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff = User::select('id', 'name')
            ->whereRaw('(deleted_at >= "'.Date::now('Europe/London')->toDateTimeString().'" OR deleted_at IS NULL)')
            ->orderByRaw('name')
            ->get();

        return view('telephones.create')->with('staff', $staff);
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
            'user_id' => 'required',
            'name'    => 'required',
            'number'  => 'required',
        ]);
        $telephoneData = [
            'name'   => $validatedData['name'],
            'number' => $validatedData['number'],
        ];
        $telephone = Telephone::create($telephoneData);

        $lookupData = [
            'user_id'      => $validatedData['user_id'],
            'telephone_id' => $telephone['id'],
        ];
        UserTelephone::create($lookupData);

        return redirect('/telephones')->with('success', 'Telephone saved');
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
        $lookup = UserTelephone::findOrFail($id);
        $telephone = Telephone::findOrFail($lookup['telephone_id']);
        $staff = User::select('id', 'name')
            ->whereRaw('(deleted_at >= "'.Date::now('Europe/London')->toDateTimeString().'" OR deleted_at IS NULL)')
            ->orderByRaw('name')
            ->get();

        return view('telephones.edit')->with(['lookup' => $lookup, 'staff' => $staff, 'telephone' => $telephone]);
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
        $lookup = UserTelephone::findOrFail($id);
        $telephone = Telephone::findOrFail($lookup['telephone_id']);

        $validatedData = $request->validate([
            'user_id' => 'required',
            'name'    => 'required',
            'number'  => 'required',
        ]);

        $telephoneData = [
            'name'   => $validatedData['name'],
            'number' => $validatedData['number'],
        ];
        if ($telephone['name'] != $telephoneData['name'] || $telephone['number'] != $telephoneData['number']) {
            $telephone = Telephone::whereId($lookup['telephone_id'])->update($telephoneData);
        }

        $lookupData = [
            'user_id'      => (int) $validatedData['user_id'],
            'telephone_id' => $telephone['id'],
        ];
        if ($lookup['user_id'] != $lookupData['user_id']) {
            UserTelephone::whereId($lookup['id'])->update($lookupData);
        }

        return redirect('/telephones')->with('success', 'Telephone updated');
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
        $lookup = UserTelephone::findOrFail($id);
        $telephone = Telephone::findOrFail($lookup['telephone_id']);
        $telephone->delete();
        $lookup->delete();

        return redirect('/telephones')->with('success', 'Telephone deleted');
    }
}
