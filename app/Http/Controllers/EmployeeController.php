<?php

namespace App\Http\Controllers;

use App\Employee;
use App\User;
use App\WorkState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Start small, work up to everything...
        $staff = User::select('users.id', 'name', 'e.started_at', 'e.ended_at', 'users.deleted_at', 'e.holiday_entitlement', 'e.holiday_carried_forward', 'e.days_per_week', 'w.workstate')
            ->join('employees AS e', 'users.id', '=', 'e.id')
            ->join('workstates AS w', 'e.default_workstate_id', '=', 'w.id')
            ->withTrashed()
            ->get();
        $dt = Date::now()->timezone('Europe/London');

        return view('staff.index')->with(['staff' => $staff, 'dt' => $dt]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('staff.create');
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
            'name'     => 'required',
            'username' => 'required',
            'start'    => 'required',
        ]);

        $staffData['name'] = $validatedData['name'];
        $staffData['username'] = $validatedData['username'];
        $staffData['email'] = 'changeme@ggpsystems.co.uk'; // Default value for new staff
        $staffData['password'] = 'Passw0rd'; // Default value for new staff
        User::create($staffData);

        $employeeData['started_at'] = $validatedData['start'].' 09:00:00';
        $employeeData['holiday_entitlement'] = 20.0; // Default value for new staff
        $employeeData['holiday_carried_forward'] = 0.0; // Default value for new staff
        $employeeData['days_per_week'] = 5; // Default value for new staff
        $employeeData['default_workstate_id'] = 1; // Default value for new staff
        Employee::create($employeeData);

        return redirect('/staff')->with('success', 'Staff member saved');
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
        $employee = User::findOrFail($id); // Kludgey, but necessary
        $staff = User::select('users.id', 'name', 'username', 'e.started_at', 'e.ended_at', 'e.holiday_entitlement', 'e.holiday_carried_forward', 'e.days_per_week', 'e.default_workstate_id')
            ->join('employees AS e', 'users.id', '=', 'e.id')
            ->where('users.id', '=', $employee->id)
            ->get();
        $staff->map(function ($user) {
            $user['started_at'] = Date::parse($user['started_at'], 'Europe/London')->toDateString();
            if (!is_null($user['ended_at'])) {
                $user['ended_at'] = Date::parse($user['ended_at'], 'Europe/London')->toDateString();
            }

            return $user;
        });

        $workstates = WorkState::all();

        return view('staff.edit')->with([
            'staff'      => $staff[0],
            'workstates' => $workstates,
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
            'name'            => 'required',
            'username'        => 'required',
            'start'           => 'required|date',
            'end'             => 'date|nullable',
            'entitlement'     => 'required|numeric',
            'carried_forward' => 'required|numeric',
            'days_per_week'   => 'required|numeric',
            'workstate_id'    => 'required|numeric',
        ]);

        $staffData = [
            'name'     => $validatedData['name'],
            'username' => $validatedData['username'],
        ];

        $employeeData = [
            'started_at'              => $validatedData['start'],
            'ended_at'                => $validatedData['end'],
            'holiday_entitlement'     => $validatedData['entitlement'],
            'holiday_carried_forward' => $validatedData['carried_forward'],
            'days_per_week'           => $validatedData['days_per_week'],
        ];

        User::whereId($id)->update($staffData);
        Employee::whereId($id)->update($employeeData);

        return redirect('/staff')->with('success', 'Staff member updated');
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
        $staff = Employee::findOrFail($id);
        $staff->delete();

        $staff = User::findOrFail($id);
        $staff->delete();

        return redirect('/staff')->with('success', 'Staff member deleted');
    }
}
