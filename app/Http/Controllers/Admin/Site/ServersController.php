<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Battlefield\Server\Server as Server;
use BFACP\Battlefield\Setting as Setting;
use BFACP\Exceptions\UptimeRobotException;
use BFACP\Http\Controllers\Controller;
use BFACP\Jobs\DeleteServerJob;
use BFACP\Libraries\Battlelog\BattlelogServer;
use BFACP\Libraries\UptimeRobot;
use Exception as Exception;
use Former\Facades\Former as Former;
use Illuminate\Support\Facades\Session as Session;

/**
 * Class ServersController.
 */
class ServersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('permission:admin.site.settings.server');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $servers = Server::with('game', 'setting')->where('GameID', '<>', 0)->get();

        $page_title = trans('navigation.admin.site.items.servers.title');

        return view('admin.site.servers.index', compact('servers', 'page_title'));
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Server $server)
    {
        $id = $server->ServerID;

        // If no setting entry exists for the server we need to create it
        if (is_null($server->setting)) {
            try {
                $battlelog = app(BattlelogServer::class)->server($server);
                $serverguid = $battlelog->guid();

                if (empty($serverguid)) {
                    throw new Exception('Battlelog returned an empty string for the GUID.');
                }
            } catch (Exception $e) {
                $serverguid = null;
                Session::flash('warnings', [
                    'Unable to automatically get the battlelog server guid. Please manually enter it.',
                ]);
            }

            $setting = new Setting(['server_id' => $id, 'battlelog_guid' => $serverguid]);
            $setting->server()->associate($server)->save();
            $server->load('setting');
        }

        Former::populate($server->setting);

        $page_title = trans('navigation.admin.site.items.servers.items.edit', ['servername' => $server->ServerName]);

        return view('admin.site.servers.edit', compact('server', 'page_title'));
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Server $server)
    {
        $id = $server->ServerID;
        $setting = $server->setting;

        if ($this->request->has('rcon_password') && !empty(trim($this->request->get('rcon_password')))) {
            $password = $this->request->get('rcon_password');
            $setting->rcon_password = trim($password);
            $this->log->info(sprintf('%s updated RCON password for server %s.', $this->user->username,
                $server->ServerName));
        }

        if ($this->request->has('filter')) {
            $chars = array_map('trim', explode(',', $this->request->get('filter')));
            $setting->filter = implode(',', $chars);
        } else {
            $setting->filter = null;
        }

        if ($this->request->has('battlelog_guid')) {
            $setting->battlelog_guid = trim($this->request->get('battlelog_guid'));
        } else {
            $setting->battlelog_guid = null;
        }

        if ($this->config->get('bfacp.uptimerobot.enabled')) {
            try {
                $uptimerobot = app(UptimeRobot::class);

                if (empty($setting->monitor_key) && $this->request->has('use_uptimerobot')) {
                    $robot_id = $uptimerobot->createMonitor($server);
                    $setting->monitor_key = $robot_id;
                    $this->log->info(sprintf('%s added server %s to UptimeRobot.', $this->user->username,
                        $server->ServerName));
                    $this->messages[] = 'Server successfully added to UptimeRobot!';
                } else if (!empty($setting->monitor_key) && !$this->request->has('use_uptimerobot')) {
                    $uptimerobot->deleteMonitor($server);
                    $setting->monitor_key = null;
                    $this->log->info(sprintf('%s removed server %s from UptimeRobot.', $this->user->username,
                        $server->ServerName));
                    $this->messages[] = 'Server successfully removed to UptimeRobot!';
                }
            } catch (UptimeRobotException $e) {
                Session::flash('warnings', [
                    $e->getMessage(),
                ]);
            }
        }

        $setting->save();

        $server->ConnectionState = $this->request->get('server_status', 'off');
        $server->save();

        $this->messages[] = sprintf('Successfully Updated %s', $server->ServerName);

        return redirect()->route('admin.site.servers.edit', [$id])->with('messages', $this->messages);
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function destroy(Server $server)
    {
        $this->dispatch(new DeleteServerJob($server));

        $this->messages[] = sprintf('Server "%s" will be deleted in the background.', $server->ServerName);

        return redirect()->route('admin.site.servers.index', [$server->ServerID])->with('messages', $this->messages);
    }
}
