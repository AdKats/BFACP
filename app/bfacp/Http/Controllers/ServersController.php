<?php namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Server\Server;
use Illuminate\Support\Facades\View;

class ServersController extends BaseController
{
    public function index()
    {
        $servers = Server::active()->with('stats')->get();

        return View::make('servers', compact('servers'));
    }
}
