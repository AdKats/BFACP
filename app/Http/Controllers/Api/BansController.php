<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Adkats\Ban;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\BanRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Roumen\Feed\Facades\Feed;

/**
 * Class BansController.
 */
class BansController extends Controller
{
    private $repository;

    /**
     * @param BanRepository $repo
     */
    public function __construct(BanRepository $repo)
    {
        parent::__construct();

        $this->repository = $repo;
    }

    /**
     * @return mixed
     */
    public function latest()
    {
        if ($this->isLoggedIn && Input::has('personal') && Input::get('personal') == 'true') {
            $isCached = false;

            $bans = $this->repository->getPersonalBans($this->user->settings()->playerIds());
        } else {
            $isCached = $this->cache->has('bans.latest');

            $bans = $this->repository->getLatestBans();
        }

        if (Input::has('type') && Input::get('type') == 'rss') {
            $feed = Feed::make();

            $feed->title = sprintf('Latest Battlefield Bans by %s', Config::get('bfacp.site.title'));
            $feed->description = sprintf('Latest Battlefield Bans by %s', Config::get('bfacp.site.title'));
            $feed->setDateFormat('datetime');
            $feed->link = URL::to('api/bans/latest?type=rss');
            $feed->lang = 'en';

            foreach ($bans as $ban) {
                $title = sprintf('%s banned for %s', $ban['player']['SoldierName'], $ban['record']['record_message']);

                $view = view('system.rss.ban_entry_content', [
                    'playerId'   => $ban['player']['PlayerID'],
                    'playerName' => $ban['player']['SoldierName'],
                    'banreason'  => $ban['record']['record_message'],
                    'sourceName' => $ban['record']['source_name'],
                    'sourceId'   => $ban['record']['source_id'],
                    'banReason'  => $ban['record']['record_message'],
                ]);

                $feed->add($title, $ban['record']['source_name'], $ban['player']['profile_url'], $ban['ban_startTime'],
                    $title, $view->render());
            }

            return $feed->render('atom');
        }

        return MainHelper::response([
            'cols' => trans('dashboard.bans.columns'),
            'bans' => $bans,
        ], null, null, null, $isCached, true);
    }

    /**
     * @return mixed
     */
    public function stats()
    {
        $yesterdaysBans = $this->cache->remember('bans.stats.yesterday', 120, function () {
            return Ban::yesterday()->count();
        });

        $avgBansPerDay = $this->cache->remember('bans.stats.average', 180, function () {
            $result = head(DB::select(File::get(storage_path().'/sql/avgBansPerDay.sql')));

            return intval($result->total);
        });

        return MainHelper::response([
            'bans' => [
                'yesterday' => $yesterdaysBans,
                'average'   => $avgBansPerDay,
            ],
        ], null, null, null, false, true);
    }
}
