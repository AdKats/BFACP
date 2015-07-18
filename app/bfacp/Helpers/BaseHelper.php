<?php namespace BFACP\Helpers;

use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Response;

class BaseHelper
{
    /**
     * @var \Illuminate\Cache\Repository
     */
    protected $cache;

    /**
     * @var \Carbon\Carbon
     */
    protected $carbon;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Support\Facades\Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->cache = App::make('Illuminate\Cache\Repository');
        $this->carbon = App::make('Carbon\Carbon');
        $this->guzzle = App::make('guzzle');
        $this->request = App::make('Illuminate\Http\Request');
        $this->response = $response;
    }
}
