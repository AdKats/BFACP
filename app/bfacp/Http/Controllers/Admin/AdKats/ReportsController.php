<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Record as Record;
use BFACP\Http\Controllers\BaseController;
use Illuminate\Support\Facades\View as View;

class ReportsController extends BaseController
{
    public function index()
    {
        $reports = Record::with('type', 'action')
            ->whereIn('command_action', [18, 20])
            ->orderBy('record_id', 'desc')
            ->paginate(30);

        return View::make('admin.adkats.reports.index', compact('reports'))->with('page_title', false);
    }
}
