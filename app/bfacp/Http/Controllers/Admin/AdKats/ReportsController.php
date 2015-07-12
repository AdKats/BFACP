<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\Http\Controllers\BaseController;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\View as View;

class ReportsController extends BaseController
{
    public function index()
    {
        $r = App::make('BFACP\Repositories\ReportRepository');

        $reports = $r->getReports(true);
        $commands = $r->getActions();

        return View::make('admin.adkats.reports.index', compact('reports', 'commands'))->with('page_title', false);
    }
}
