<?php

namespace App\Http\Controllers;

use App\Audit;
use App\User;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $audit = Audit::orderBy('created_at', 'desc')
            ->paginate();

        $audit->map(function ($entry) {
            $entry['user_name'] = User::where('id', $entry['user_id'])->pluck('name')->first();

            return $entry;
        });

        return view('audit.index')->with(['audit' => $audit]);
    }
}
