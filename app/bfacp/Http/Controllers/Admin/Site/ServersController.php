<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Battlefield\Server\Server as Server;
use BFACP\Battlefield\Setting as Setting;
use BFACP\Http\Controllers\BaseController;
use Exception as Exception;
use Former\Facades\Former as Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\Session as Session;
use Illuminate\Support\Facades\View as View;

class ServersController extends BaseController
{
    public function index()
    {
        $servers = Server::all();

        return View::make('admin.site.servers.index', compact('servers'))->with('page_title', 'Servers');
    }

    public function edit($id)
    {
        try {
            $server = Server::findOrFail($id);

            // If no setting entry exists for the server we need to create it
            if (is_null($server->setting)) {

                try {
                    $battlelog = App::make('BFACP\Libraries\Battlelog\BattlelogServer')->server($server);
                    $serverguid = $battlelog->guid();
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

            return View::make('admin.site.servers.edit', compact('server'))->with('page_title', 'Server Settings');
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.servers.index')->withErrors(['Server doesn\'t exist.']);
        }
    }

    public function update($id)
    {
        try {
            $server = Server::findOrFail($id);
            $setting = $server->setting;

            if (Input::has('rcon_password') && !empty(trim(Input::get('rcon_password')))) {
                $password = Input::get('rcon_password');
                $setting->rcon_password = trim($password);
            }

            if (Input::has('filter')) {
                $chars = array_map('trim', explode(',', Input::get('filter')));
                $setting->filter = implode(',', $chars);
            } else {
                $setting->filter = null;
            }

            if (Input::has('battlelog_guid')) {
                $setting->battlelog_guid = trim(Input::get('battlelog_guid'));
            } else {
                $setting->battlelog_guid = null;
            }

            $setting->save();

            $server->ConnectionState = Input::get('status', 'off');
            $server->save();

            return Redirect::route('admin.site.servers.index')->with('messages',
                [sprintf('Successfully Updated %s', $server->ServerName)]);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.servers.index')->withErrors(['Server doesn\'t exist.']);
        }
    }
}
