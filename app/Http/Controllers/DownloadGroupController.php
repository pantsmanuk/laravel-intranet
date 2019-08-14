<?php

namespace App\Http\Controllers;

use App\DownloadGroup;
use Illuminate\Http\Request;

class DownloadGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $downloads = DownloadGroup::orderBy('created_at', 'desc')->get();

        return view('downloads.index')->with(['downloads' => $downloads]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('downloads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:20',
            'files' => 'required|string|max:60',
        ]);
        DownloadGroup::create($validatedData);

        return redirect('/downloads')->with('success', 'Download group saved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $download = DownloadGroup::findOrFail($id);

        return view('downloads.edit')->with(['download' => $download]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:20',
            'files' => 'required|string|max:60',
        ]);
        DownloadGroup::whereId($id)->update($validatedData);

        return redirect('/downloads')->with('success', 'Download group updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $download = DownloadGroup::findOrFail($id);
        $download->delete();

        return redirect('/downloads')->with('success', 'Download group deleted');
    }
}
