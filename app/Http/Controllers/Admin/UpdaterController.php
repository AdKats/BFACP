<?php

namespace BFACP\Http\Controllers\Admin;

use BFACP\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;
use vierbergenlars\SemVer\version;

/**
 * Class UpdaterController.
 */
class UpdaterController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->guzzle = app('Guzzle');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $page_title = 'BFACP Versions';

        try {
            $latest_release = $this->cache->remember('latest_release', 30, function () {
                $response = $this->guzzle->get('https://api.github.com/repos/Prophet731/BFAdminCP/releases/latest');
                $latest_release = json_decode($response->getBody(), true);

                return $latest_release;
            });

            $releases = $this->cache->remember('releases', 30, function () {
                $response = $this->guzzle->get('https://api.github.com/repos/Prophet731/BFAdminCP/releases');
                $releases = json_decode($response->getBody(), true);

                return $releases;
            });

            $outofdate = version::lt(BFACP_VERSION, $latest_release['tag_name']);
            $unreleased = version::gt(BFACP_VERSION, $latest_release['tag_name']);

            return view('system.updater.index',
                compact('page_title', 'releases', 'outofdate', 'latest_release', 'unreleased'));
        } catch (RequestException $e) {
            return response($e->getMessage(), 500);
        }
    }
}
