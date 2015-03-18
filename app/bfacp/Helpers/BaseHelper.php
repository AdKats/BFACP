<?php namespace BFACP\Helpers;

use Carbon\Carbon;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class BaseHelper
{
    protected $cache;
    protected $carbon;
    protected $guzzle;
    protected $request;
    protected $response;

    public function __construct(Cache $cache, Request $request, Response $response, Carbon $carbon, Guzzle $guzzle)
    {
        $this->cache    = $cache;
        $this->carbon   = $carbon;
        $this->guzzle   = $guzzle;
        $this->request  = $request;
        $this->response = $response;
    }
}
