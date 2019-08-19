<?php

namespace App\Http\Controllers;

use App\Absence;
use App\DoorEvent;
use App\Telephone;
use App\User;
use App\WorkState;
use Illuminate\Http\Request;
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
            $absence = Absence::select('absence_types.name AS work_state')
                ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
                ->where('absences.user_id', $employee->id)
                ->where('absences.started_at', '<=', $dt->toDateTimeString())
                ->where('absences.ended_at', '>=', $dt->toDateTimeString())
                ->first();
            if (!is_null($absence)) {
                $employee['work_state'] = $absence->work_state;
            } else {
                $employee['work_state'] = WorkState::where('id', $employee->default_work_state_id)
                    ->pluck('work_state')
                    ->first();
            }

            return $employee;
        });

        $remotes = $staff->filter(function ($employee) {
            if ($employee['work_state'] === 'Remote working') {
                $employee['door_event'] = DoorEvent::where('user_id', '=', $employee->id)
                    ->whereDate('created_at', Date::now('Europe/London'))
                    ->latest('created_at')
                    ->pluck('event')
                    ->first();
                if (is_null($employee['door_event'])) {
                    $employee['door_event'] = 1; // First touch should be an In
                }

                return $employee;
            }
        });

        return view('home')->with([
            'remotes' => $remotes,
            'staff'   => $staff,
        ]);
    }

    /**
     * Store a T&A record for remote workers.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData['user_id'] = auth()->id();
        $validatedData['event'] = (isset($request['event'])) ? 1 : 0;

        DoorEvent::create($validatedData);

        return redirect('/home');
    }
}
