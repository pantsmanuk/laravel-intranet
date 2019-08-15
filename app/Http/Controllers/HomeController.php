<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Telephone;
use App\WorkState;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Date;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dtLocal = Date::now('Europe/London');

        $staff = User::select('users.id', 'users.name', 'employees.default_work_state_id')
            ->join('employees', 'users.id', '=', 'employees.id')
            ->whereDate('users.deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('users.deleted_at')
            ->orderByRaw('users.name')
            ->get();
        $staff->map(function ($employee) {
            $dt = Date::now('Europe/London');

            $employee['extn'] = Telephone::join('users_telephones', 'telephones.id', '=', 'users_telephones.telephone_id')
                ->where('users_telephones.user_id', $employee->id)
                ->pluck('telephones.number')
                ->first();
            $employee['telephones'] = Telephone::select('telephones.name', 'telephones.number')
                ->join('users_telephones', 'telephones.id', '=', 'users_telephones.telephone_id')
                ->where('users_telephones.user_id', $employee->id)
                ->where('telephones.name', '!=', 'Extn')
                ->orderBy('telephones.name')
                ->get();
            $absence = Absence::select('absence_types.name AS workstate')
                ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
                ->where('absences.user_id', $employee->id)
                ->where('absences.start_at', '<=', $dt->toDateTimeString())
                ->where('absences.end_at', '>=', $dt->toDateTimeString())
                ->first();
            if (!is_null($absence)) {
                $employee['workstate'] = $absence->workstate;
            } else {
                $employee['workstate'] = WorkState::where('id', $employee->default_workstate_id)
                    ->pluck('work_state')
                    ->first();
            }

            return $employee;
        });

        return view('home')->with('staff', $staff);
    }
}
