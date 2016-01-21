<?php

namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Server\Server;
use Illuminate\Support\Facades\View;

/**
 * Class ServersController.
 */
class ServersController extends Controller
{
    public function index()
    {
        $servers = Server::active()->with('stats')->get();

        return View::make('servers', compact('servers'));
    }
}
