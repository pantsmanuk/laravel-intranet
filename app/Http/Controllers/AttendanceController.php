<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Attendance;
use App\Config;
use App\DoorEvent;
use App\Fob;
use App\Mail\Timesheet;
use App\User;
use App\WorkState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
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

        $active = User::select('id')
            ->whereDate('deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->get();

        // "Inject" the spare fobs so they will show up
        $active->push(['id' => 12]);
        $active->push(['id' => 13]);
        $active->push(['id' => 14]);

        $onSite = Attendance::select('empref')
            ->whereDate('doordate', $dtLocal->toDateString())
            ->whereIn('empref', $active)
            ->groupBy('empref')
            ->orderByRaw('MIN(doortime) ASC')
            ->get();
        $onSite->map(function ($employee) {
            $dt = Date::now('Europe/London');
            $user = User::find($employee->empref);

            $employee['spare_name'] = '';
            switch ($employee->empref) {
                case 12:
                case 13:
                case 14:
                    $fob_user = Fob::where('fob_id', $employee->empref)
                        ->whereDate('created_at', $dt->toDateString())
                        ->pluck('user_id')
                        ->first();
                    if (!empty($fob_user)) {
                        $employee['spare_user'] = $fob_user;
                        $employee['spare_name'] = User::find($fob_user)->name;
                    }
                    break;
            }
            if (!in_array($employee->empref, [12, 13, 14])) {
                $employee['name'] = $user->name;
                $employee['forenames'] = $user->forenames;
            } else {
                switch ($employee->empref) {
                    case 12:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #1';
                        break;
                    case 13:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #2';
                        break;
                    case 14:
                        $employee['forenames'] = 'Spare #1';
                        $employee['name'] = 'Spare #3';
                        break;
                }
            }
            $employee['door_event'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->latest('doortime')
                ->select('doorevent')
                ->first()
                ->event_type;
            $employee['door_event_time'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->latest('doortime')
                ->select('doortime')
                ->first()
                ->event_time;
            $employee['first_event'] = Attendance::whereDate('doordate', $dt->toDateString())
                ->where('empref', $employee->empref)
                ->oldest('doortime')
                ->select('doortime')
                ->first()
                ->event_time;

            return $employee;
        });

        $offSite = User::select('users.id', 'users.name', 'employees.default_work_state_id')
            ->join('employees', 'users.id', '=', 'employees.id')
            ->whereDate('users.deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('users.deleted_at')
            ->orderBy('users.name')
            ->get();
        $offSite->map(function ($employee) {
            $dt = Date::now()->toImmutable()->timezone('Europe/London');

            $absence = Absence::select('absence_types.name AS work_state', 'absences.note')
                ->join('absence_types', 'absences.absence_id', '=', 'absence_types.id')
                ->where('absences.user_id', $employee->id)
                ->where('absences.started_at', '<=', $dt->addHour()->toDateTimeString())
                ->where('absences.ended_at', '>=', $dt->toDateTimeString())
                ->first();

            if (!is_null($absence)) {
                $employee['door_event'] = $absence->work_state;
                $employee['note'] = trim($absence->note);
            } else {
                $employee['door_event'] = WorkState::where('id', $employee->default_work_state_id)
                    ->pluck('work_state')
                    ->first();
                $employee['note'] = '';
            }

            return $employee;
        });

        $onSite_users = array_merge($onSite->pluck('empref')->toArray(), $onSite->pluck('spare_user')->toArray());
        $offSite = $offSite->filter(function ($employee) use ($onSite_users) {
            if ($employee['door_event'] === 'Holiday'
                || !in_array($employee->id, $onSite_users)) {
                return $employee;
            }
        });

        return view('attendance.index')->with([
            'onSite'  => $onSite,
            'offSite' => $offSite,
        ]);
    }

    /**
     * Show the attendance timesheet.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show()
    {
        $dt = Date::now('Europe/London');
        if ($dt->format('l') != 'Monday') {
            $yesterday = $dt->subDay();
        } else {
            $yesterday = $dt->subDays(3);
        }

        $door_events = DoorEvent::where('user_id', auth()->id())
            ->whereRaw('created_at LIKE "'.$yesterday->format('Y-m-d').'%"')
            ->get();
        $event_rows = [];
        $i = 0;
        $total = 0;
        $last_event = null;
        $last_time = null;
        foreach ($door_events as $event) {
            if ($event->event == 0) {
                $event_rows[$i] = ['in' => $event->created_at->format('H:i')];
                $last_time = $event->created_at;
                $last_event = 'in';
            } else {
                $event_rows[$i] = array_merge($event_rows[$i], ['out' => $event->created_at->format('H:i')]);
                if ($last_event == 'in') {
                    $interval = $last_time->diff($event->created_at); // DateInterval
                    $total += $last_time->diffInSeconds($event->created_at); // Seconds
                    $event_rows[$i] = array_merge($event_rows[$i], ['sub_total' => $interval->format('%H:%I')]);
                    $last_event = 'out';
                    $i++;
                }
            }
        }

        $events = collect([
            'yesterday'   => $yesterday->format('j F'),
            'door_events' => $event_rows,
            'time_worked' => gmdate('H:i', $total),
        ]);

        return view('attendance.show')->with([
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dt = Date::now('Europe/London');
        if ($dt->format('l') != 'Monday') {
            $yesterday = $dt->subDay();
        } else {
            $yesterday = $dt->subDays(3);
        }

        $door_events = DoorEvent::where('user_id', auth()->id())
            ->whereRaw('created_at LIKE "'.$yesterday->format('Y-m-d').'%"')
            ->get();
        $event_rows = [];
        $i = 0;
        $total = 0;
        $last_event = null;
        $last_time = null;
        foreach ($door_events as $event) {
            if ($event->event == 0) {
                $event_rows[$i] = ['in' => $event->created_at->format('H:i')];
                $last_time = $event->created_at;
                $last_event = 'in';
            } else {
                $event_rows[$i] = array_merge($event_rows[$i], ['out' => $event->created_at->format('H:i')]);
                if ($last_event == 'in') {
                    $interval = $last_time->diff($event->created_at); // DateInterval
                    $total += $last_time->diffInSeconds($event->created_at); // Seconds
                    $event_rows[$i] = array_merge($event_rows[$i], ['sub_total' => $interval->format('%H:%I')]);
                    $last_event = 'out';
                    $i++;
                }
            }
        }

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $header = [
            'name' => 'Verdana',
            'size' => 16,
            'bold' => true,
        ];

        $text = $section->addText('Attendance Events for '.$yesterday->format('j F'), $header);
        $text = $section->addText(auth()->user()->name, ['name'=> 'Verdana', 'size' => 14, 'bold' => true]);
        $text = $section->addText(' ', ['name'=> 'Verdana', 'size' => 14]);

        $table = $section->addTable();

        // Header
        $table->addRow();
        $table->addCell(2000)->addText('In Time', ['name'=> 'Verdana', 'bold' => true]);
        $table->addCell(2000)->addText('Out Time', ['name'=> 'Verdana', 'bold' => true]);
        $table->addCell(2000)->addText('Sub-total', ['name'=> 'Verdana', 'bold' => true]);

        // "Data" rows here...
        foreach ($event_rows as $row) {
            $table->addRow();
            $table->addCell(2000)->addText($row['in']);
            $table->addCell(2000)->addText($row['out']);
            $table->addCell(2000)->addText($row['sub_total']);
        }

        // Footer
        $table->addRow();
        $table->addCell(4000, ['gridSpan' => 2])->addText('Time Working:', ['name'=> 'Verdana', 'bold' => true]);
        $table->addCell(2000)->addText(gmdate('H:i', $total), ['bold' => true]);

        $filename = $yesterday->format('d-m-Y').'.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filename);

        Mail::to(Config::where('name', '=', 'email_attendance')->pluck('value')->first())
            ->send(new Timesheet([
                'attachment_filename' => $filename,
                'timesheet_date'      => $yesterday->format('j F'),
                'user_name'           => auth()->user()->name,
            ]));

        return redirect('/home')->with('success', 'Timesheet email sent');
    }
}
