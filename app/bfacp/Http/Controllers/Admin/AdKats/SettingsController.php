<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\Adkats\Setting;
use BFACP\Battlefield\Server\Server;
use BFACP\Http\Controllers\BaseController;
use Illuminate\Support\Facades\View;

class SettingsController extends BaseController
{
    public function index()
    {
        $servers = Server::all();

        return View::make('admin.adkats.settings.index', compact('servers'))->with('page_title',
            'AdKats Settings Server List');
    }

    public function edit($id)
    {
        $settings = Setting::where('server_id', $id)->get();

        return View::make('admin.adkats.settings.edit', compact('settings'))->with('page_title',
            sprintf('AdKats Settings for #%s', $id));
    }
}
