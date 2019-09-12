<?php

namespace App\Http\Controllers;

use App\DownloadGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownloadController extends Controller
{
    /**
     * Show the download log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $id = DownloadGroup::pluck('id')
            ->last();

        $download_groups = DownloadGroup::all();

        $downloads = DB::connection('wordpress')
            ->table('wp_ggp_download_monitor_log AS log')
            ->select([
                'log.date',
                'user.user_email AS email',
                'file.title AS filename',
            ])
            ->join('wp_ggp_users AS user', 'log.user_id', '=', 'user.id')
            ->join('wp_ggp_download_monitor_files AS file', 'log.download_id', '=', 'file.id')
            ->whereRaw('log.download_id IN('.$download_groups->where('id', '=', $id)->pluck('files')->first().')')
            ->whereRaw('log.ip_address NOT LIKE ("10.0.0.%")')
            ->orderBy('log.date')
            ->get();

        return view('log.index')->with([
            'id'              => $id,
            'downloads'       => $downloads,
            'download_groups' => $download_groups,
            ]);
    }

    /**
     * Show the download log based on user selection.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function select(Request $request)
    {
        $id = (int) $request->input('id');

        $download_groups = DownloadGroup::all();

        $downloads = DB::connection('wordpress')
            ->table('wp_ggp_download_monitor_log AS log')
            ->select([
                'log.date',
                'user.user_email AS email',
                'file.title AS filename',
            ])
            ->join('wp_ggp_users AS user', 'log.user_id', '=', 'user.id')
            ->join('wp_ggp_download_monitor_files AS file', 'log.download_id', '=', 'file.id')
            ->whereRaw('log.download_id IN('.$download_groups->where('id', '=', $id)->pluck('files')->first().')')
            ->whereRaw('log.ip_address NOT LIKE ("10.0.0.%")')
            ->orderBy('log.date')
            ->get();

        return view('log.index')->with([
            'id'              => $id,
            'downloads'       => $downloads,
            'download_groups' => $download_groups,
        ]);
    }
}
