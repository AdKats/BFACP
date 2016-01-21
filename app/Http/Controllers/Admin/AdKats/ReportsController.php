<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Http\Controllers\Controller;
use Illuminate\Support\Facades\App as App;

/**
 * Class ReportsController
 * @package BFACP\Http\Controllers\Admin\Adkats
 */
class ReportsController extends Controller
{

    /**
     * @return $this
     */
    public function index()
    {
        $r = App::make('BFACP\Repositories\ReportRepository');

        $reports = $r->getReports(true);
        $commands = $r->getActions();

        return view('admin.adkats.reports.index', compact('reports', 'commands'))->with('page_title', false);
    }
}
