<?php namespace BFACP\Http\Controllers\Admin;

use BFACP\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use vierbergenlars\SemVer\version;

class UpdaterController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->guzzle = app('Guzzle');
    }

    public function index()
    {
        $page_title = 'BFAdminCP Updater';

        $latest_release = Cache::remember('latest_release', 30, function () {
            $response = $this->guzzle->get('https://api.github.com/repos/Prophet731/BFAdminCP/releases/latest');
            $latest_release = json_decode($response->getBody());

            return $latest_release;
        });

        $releases = Cache::remember('releases', 30, function () {
            $response = $this->guzzle->get('https://api.github.com/repos/Prophet731/BFAdminCP/releases');
            $releases = json_decode($response->getBody());

            return $releases;
        });

        $outofdate = version::lt(BFACP_VERSION, $latest_release['tag_name']);
        $unreleased = version::gt(BFACP_VERSION, $latest_release['tag_name']);

        return View::make('system.updater.index',
            compact('page_title', 'releases', 'outofdate', 'latest_release', 'unreleased'));
    }
}