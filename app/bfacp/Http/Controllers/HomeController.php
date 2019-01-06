<?php namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Server\Server;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class HomeController extends BaseController
{
    /**
     * Shows the dashboard
     */
    public function index()
    {
        $playerRepository = App::make('BFACP\Repositories\PlayerRepository');

        // Cache results for 1 day
        $uniquePlayers = Cache::remember('players.unique.total', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayerCount();
        });

        // Cache results for 1 day
        $adkats_statistics = Cache::remember('adkats.statistics', 60 * 24, function () use (&$playerRepository) {
            $results = DB::select(File::get(storage_path() . '/sql/adkats_statistics.sql'));

            return head($results);
        });

        // Cache results for 1 day
        $countryMap = Cache::remember('players.seen.country', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayersSeenByCountry();
        });

        $countryMapTable = $countryMap->sortByDesc('total')->take(5);

        return View::make('dashboard',
            compact('uniquePlayers', 'adkats_statistics', 'countryMap', 'countryMapTable'))->with('page_title',
            Lang::get('navigation.main.items.dashboard.title'));
    }

    /**
     * Shows the live scoreboard
     */
    public function scoreboard()
    {
        $servers = [
            '-1' => 'Select Server&hellip;',
        ];

        Server::active()->get()->each(function ($server) use (&$servers) {
            $servers[ $server->game->Name ][ $server->ServerID ] = $server->ServerName;
        });

        if ($this->isLoggedIn && $this->user->ability(null, Cache::get('admin.perm.list')['scoreboard'])) {

            $perms = $this->user->ability(null, Cache::get('admin.perm.list')['scoreboard'],
                ['return_type' => 'array']);
            $permissions = $perms['permissions'];

            $validPermissions = $this->user->roles[0]->perms->filter(function ($perm) use (&$permissions) {
                if (array_key_exists($perm->name, $permissions) && $permissions[ $perm->name ]) {
                    return true;
                }
            })->map(function ($perm) {
                $p = clone($perm);
                $p->name = explode('.', $perm->name)[2];

                return $p;
            })->lists('display_name', 'name');
	
    	    // compact throws a warning if a value is undefined
 	    set_error_handler(function() { /* ignore errors */ });
            $adminview = View::make('partials.scoreboard.admin.admin',
                compact('validPermissions', 'presetMessages'))->render();
	    restore_error_handler();
        } else {
            $adminview = null;
        }

        return View::make('scoreboard', compact('servers', 'adminview'))->with('page_title',
            Lang::get('navigation.main.items.scoreboard.title'));
    }
}
