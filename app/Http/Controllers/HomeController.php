<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\Staff;
use App\Telephone;
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

        $staff = Staff::select('staff_id', 'name', 'default_workstate')
            ->whereDate('deleted_at', '>=', $dtLocal->toDateTimeString())
            ->orWhereNull('deleted_at')
            ->orderByRaw('firstname')
            ->get();
        $staff->map(function ($employee) {
            $dt = Date::now('Europe/London');
            $workstate_arr = array(1=>"On-site",
                2=>"Remote working",
                3=>"Not working");

            $employee['extn'] = Telephone::join('staff_telephone_rel', 'telephone.id', '=', 'staff_telephone_rel.telephone_id')
                ->where('staff_telephone_rel.staff_id', $employee->staff_id)
                ->pluck('telephone.number')
                ->first();
            $employee['telephones'] = Telephone::select('telephone.name','telephone.number')
                ->join('staff_telephone_rel', 'telephone.id', '=', 'staff_telephone_rel.telephone_id')
                ->where('staff_telephone_rel.staff_id', $employee->staff_id)
                ->where('telephone.name', '!=', 'Extn')
                ->orderBy('telephone.name')
                ->get();
            $absence = Holiday::select('absence_lookup.name AS workstate')
                ->join('absence_lookup','holidays.absence_id','=','absence_lookup.id')
                ->where('staff_id',$employee->staff_id)
                ->where('start','<=',$dt->toDateTimeString())
                ->where('end','>=',$dt->toDateTimeString())
                ->first();
            if(!is_null($absence)) {
                $employee['workstate'] = $absence->workstate;
            } else {
                $employee['workstate'] = $workstate_arr[$employee->default_workstate];
            }
            return $employee;
        });

        return view('home')->with('staff', $staff);
    }
}
