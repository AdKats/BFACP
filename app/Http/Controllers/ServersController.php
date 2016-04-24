<?php

namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Server\Server;

/**
 * Class ServersController.
 */
class ServersController extends Controller
{
    /**
     * Returns list of active servers.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $servers = Server::active()->with('stats')->get();

        $page_title = trans('navigation.main.items.servers.list.title');

        if ($this->request->get('type') == 'json') {
            return $servers;
        }

        return view('servers.list', compact('servers', 'page_title'));
    }

    /**
     * Shows the selected server stats.
     *
     * @param Server $server
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Server $server)
    {
        $page_title = sprintf('[%s] %s', $server->game->Name, ($server->server_name_short ?: $server->ServerName));

        return view('servers.show', compact('server', 'page_title'));
    }

    /**
     * Shows the live scoreboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function scoreboard()
    {
        $servers = [
            '-1' => 'Select Server&hellip;',
        ];

        Server::active()->get()->each(function ($server) use (&$servers) {
            $servers[$server->game->Name][$server->ServerID] = $server->ServerName;
        });

        if ($this->isLoggedIn && $this->user->ability(null, $this->cache->get('admin.perm.list')['scoreboard'])) {
            $perms = $this->user->ability(null, $this->cache->get('admin.perm.list')['scoreboard'],
                ['return_type' => 'array']);
            $permissions = $perms['permissions'];

            $validPermissions = $this->user->roles[0]->perms->filter(function ($perm) use (&$permissions) {
                $permName = (string) $perm->name;
                if (array_key_exists($permName, $permissions) && $permissions[$permName]) {
                    return true;
                }
            })->lists('display_name', 'key_name');

            $adminview = view('partials.scoreboard.admin.admin',
                compact('validPermissions', 'presetMessages'))->render();
        } else {
            $adminview = null;
        }

        $page_title = trans('navigation.main.items.servers.scoreboard.title');

        return view('servers.scoreboard', compact('servers', 'adminview', 'page_title'));
    }
}
