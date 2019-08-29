<?php

namespace App\Http\Controllers;

use App\Salary;
use Illuminate\Support\Facades\Crypt;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('salary.index');
    }

    /**
     * Approve the specified resource in storage.
     *
     * @param string $secret
     *
     * @return \Illuminate\Http\Response
     */
    public function authorise($secret)
    {
        $aRequest = explode('%', Crypt::decryptString($secret));
        $data['id'] = (int) $aRequest[0];
        $data['run_date'] = $aRequest[1];

        $salary = Salary::findOrFail($data['id']);

        if ($salary->run_date->toDateTimeString() === $data['run_date']) {
            dd($salary->run_date, $data['run_date']);
            //$salary->update(['approved' => -1]);

            return redirect('/salary')->with('success', 'Salary run authorised');
        } else {
            return redirect('/salary')->with('errors', 'Salary run authorisation error');
        }

    }

    /**
     * Output a "test secret".
     *
     * @todo REMOVE BEFORE PRODUCTION TESTING BEGINS
     */
    public function dummy()
    {
        $data = Salary::latest('run_date')->firstOrFail();

        $secret = Crypt::encryptString($data->id.'%'.$data->run_date);
        echo 'http://attendance.test/salary/'.$secret.'/authorise'.PHP_EOL;
    }
}
