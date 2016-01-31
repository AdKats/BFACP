<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Http\Controllers\Controller;
use BFACP\Repositories\ReportRepository;

/**
 * Class ReportsController.
 */
class ReportsController extends Controller
{
    /**
     * @return $this
     */
    public function index()
    {
        $r = app(ReportRepository::class);

        $reports = $r->getReports(true);
        $commands = $r->getActions();

        return view('admin.adkats.reports.index', compact('reports', 'commands'))->with('page_title', false);
    }
}
