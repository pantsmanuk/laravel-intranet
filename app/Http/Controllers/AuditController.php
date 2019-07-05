<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Foundation\Auth\User;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $audit = Audit::all();
        $audit->map(function ($entry) {
            $entry['user_name'] = User::find($entry['user_id'])->name;

            return $entry;
        });

        return view('audit.index')->with(['audit' => $audit]);
    }
}
