<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Adkats\Ban;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\BanRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

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
                'average' => $avgBansPerDay,
            ],
        ], null, null, null, false, true);
    }
}
