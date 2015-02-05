<?php namespace BFACP\Http\Controllers\Api;

use BFACP\AdKats\Ban;
use BFACP\Repositories\BanRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use MainHelper;

class BansController extends BaseController
{
    private $repository;

    public functino __construct(BanRepository $repo)
    {
        $this->repository = $repo;
    }

    public function latest()
    {
        if($this->isLoggedIn && $this->request->has('personal') && $this->request->get('personal') == 'true')
        {
            $isCached = FALSE;
            $bans = Ban::with('player', 'record')->personal()->get()->toArray();
        }
        else
        {
            $isCached = Cache::has('bans.latest');

            $bans = $this->repository->getLatestBans();
        }

        return MainHelper::response([
            'cols' => Lang::get('dashboard.bans.columns'),
            'bans' => $bans
        ], NULL, NULL, NULL, $isCached, TRUE);
    }

    public function stats()
    {
        $yesterdaysBans = Cache::remember('bans.stats.yesterday', 120, function() {
            return Ban::yesterday()->count();
        });

        $avgBansPerDay = Cache::remember('bans.stats.average', 180, function() {
            $result = head(DB::select(File::get(storage_path() . '/sql/avgBansPerDay.sql')));
            return intval($result->total);
        });

        return MainHelper::response([
            'bans' => [
                'yesterday' => $yesterdaysBans,
                'average' => $avgBansPerDay
            ]
        ]);
    }
}
