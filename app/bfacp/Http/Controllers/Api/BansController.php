<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Adkats\Ban;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\BanRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Roumen\Feed\Facades\Feed;

class BansController extends BaseController
{
    private $repository;

    public function __construct(BanRepository $repo)
    {
        parent::__construct();

        $this->repository = $repo;
    }

    public function latest()
    {
        if ($this->isLoggedIn && $this->request->has('personal') && $this->request->get('personal') == 'true') {
            $isCached = false;

            $bans = $this->repository->getPersonalBans($this->user->settings()->playerIds());
        } else {
            $isCached = Cache::has('bans.latest');

            $bans = $this->repository->getLatestBans();
        }

        if ($this->request->has('type') && $this->request->get('type') == 'rss') {
            $feed = Feed::make();

            $feed->title = sprintf('Latest Battlefield Bans by %s', Config::get('bfacp.site.title'));
            $feed->description = sprintf('Latest Battlefield Bans by %s', Config::get('bfacp.site.title'));
            $feed->setDateFormat('datetime');
            $feed->link = URL::to('api/bans/latest?type=rss');
            $feed->lang = 'en';

            foreach ($bans as $ban) {
                $title = sprintf('%s banned for %s', $ban['player']['SoldierName'], $ban['record']['record_message']);

                $view = View::make('system.rss.ban_entry_content', [
                    'playerId'   => $ban['player']['PlayerID'],
                    'playerName' => $ban['player']['SoldierName'],
                    'banreason'  => $ban['record']['record_message'],
                    'sourceName' => $ban['record']['source_name'],
                    'sourceId'   => $ban['record']['source_id'],
                    'banReason'  => $ban['record']['record_message'],
                ]);

                $feed->add(
                    $title,
                    $ban['record']['source_name'],
                    $ban['player']['profile_url'],
                    $ban['ban_startTime'],
                    $title,
                    $view->render()
                );
            }

            return $feed->render('atom');
        }

        return MainHelper::response([
            'cols' => Lang::get('dashboard.bans.columns'),
            'bans' => $bans,
        ], null, null, null, $isCached, true);
    }

    public function stats()
    {
        $yesterdaysBans = Cache::remember('bans.stats.yesterday', 120, function () {
            return Ban::yesterday()->count();
        });

        $avgBansPerDay = Cache::remember('bans.stats.average', 180, function () {
            $result = head(DB::select(File::get(storage_path() . '/sql/avgBansPerDay.sql')));

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
