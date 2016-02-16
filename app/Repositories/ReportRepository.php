<?php

namespace BFACP\Repositories;

use BFACP\Adkats\Command;
use BFACP\Adkats\Record;
use Illuminate\Support\Facades\Cache;

/**
 * Class ReportRepository.
 */
class ReportRepository extends BaseRepository
{
    /**
     * Commands allowed for reports.
     *
     * @var array
     */
    public static $allowedCommands = [3, 6, 7, 8, 9, 10, 11, 40, 42, 62];

    /**
     * @return array
     */
    public static function getAllowedCommands()
    {
        return self::$allowedCommands;
    }

    /**
     * Actions allowed to be used on reports.
     *
     * @return array
     */
    public function getActions()
    {
        $actions = Cache::remember('system.adkats.reports.actions', 60 * 12, function () {
            return Command::whereIn('command_id', static::$allowedCommands)->pluck('command_name', 'command_id');
        });

        return $actions;
    }

    /**
     * Returns the latest reports.
     *
     * @param bool $paginate Paginate response
     * @param int  $take     Get X amount
     *
     * @return array
     */
    public function getReports($paginate = false, $take = 30)
    {
        $reports = Record::with('server', 'type', 'action')->whereIn('command_type', [18, 20])->orderBy('record_time',
            'desc');

        if ($this->request->has('last_id')) {
            $lastId = $this->request->get('last_id', null);

            if (! is_null($lastId) && is_numeric($lastId)) {
                $reports->where('record_id', '>', abs($lastId));
            }
        }

        if ($paginate !== false) {
            $reports = $reports->paginate($take);
        } else {
            $reports = $reports->take($take)->get();
        }

        return $reports;
    }

    /**
     * Get's a report by its ID.
     *
     * @param int $id
     *
     * @return object
     */
    public function getReportById($id)
    {
        return Record::with('server', 'type', 'action')->whereIn('command_type', [18, 20])->findOrFail($id);
    }
}
