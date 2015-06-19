<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Command as Command;
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

        $commands = Command::whereIn('command_id', [3, 6, 7, 8, 9, 10, 11, 40, 41, 61])->lists('command_name', 'command_id');

        return View::make('admin.adkats.reports.index', compact('reports', 'commands'))->with('page_title', false);
    }
}
