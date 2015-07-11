<?php namespace BFACP\Repositories;

use BFACP\AdKats\Command;
use BFACP\AdKats\Record;
use Illuminate\Support\Facades\Cache;

class ReportRepository extends BaseRepository
{
    /**
     * Commands allowed for reports
     * @var array
     */
    static public $allowedCommands = [3, 6, 7, 8, 9, 10, 11, 40, 41, 61];

    /**
     * Actions allowed to be used on reports
     * @return array
     */
    public function getActions()
    {
        $actions = Cache::remember('system.adkats.reports.actions', 60 * 12, function () {
            return Command::whereIn('command_id', static::$allowedCommands)->lists('command_name', 'command_id');
        });

        return $actions;
    }

    /**
     * Returns the latest reports
     * @param  boolean $paginate Paginate response
     * @param  integer $take     Get X amount
     * @return array
     */
    public function getReports($paginate = false, $take = 30)
    {
        $reports = Record::with('server', 'type', 'action')->whereIn('command_action', [18, 20])->orderBy('record_time', 'desc');

        if($paginate !== false) {
            $reports = $reports->paginate($take);
        } else {
            $reports = $reports->take($take)->get();
        }

        return $reports;
    }

    /**
     * Get's a report by its ID
     * @param  integer $id
     * @return object
     */
    public function getRecordById($id)
    {
        return Record::with('server', 'type', 'action')->findOrFail($id);
    }
}
