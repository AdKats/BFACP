<?php namespace BFACP\Libraries;

use BFACP\Exceptions\MetabansException;
use GuzzleHttp\Client AS Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use MainHelper;

class Metabans
{
    /**
     * Metabans API URL
     */
    const URL = "http://www.metabans.com/api";

    /**
     * API Key Hash
     * @var string
     */
    private $hash = '';

    /**
     * API Key
     * @var string
     */
    private $key = '';

    /**
     * API Username
     * @var string
     */
    private $user = '';

    /**
     * Used to pull information from accounts.
     *
     * Feeds, Assessments, etc..
     * @var string
     */
    private $account = '';

    /**
     * API Salt
     * @var string
     */
    private $salt = '';

    /**
     * Auth creds
     * @var array
     */
    private $auth = [];

    /**
     * Valid assessment types
     * @var array
     */
    public $assessment_types = [
        'none',
        'watch',
        'white',
        'black'
    ];

    /**
     * Supported Games
     * @var array
     */
    public $supported_games = [
        'BF_BC2',
        'MOH_2010',
        'MOH_2012',
        'BF_3',
        'BF_4'
    ];

    /**
     * GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Base62
     */
    protected $base62;

    public function __construct(Guzzle $guzzle, Base62 $base62)
    {
        $this->key     = Config::get('bfacp.metabans.key', FALSE);
        $this->user    = Config::get('bfacp.metabans.user', FALSE);
        $this->account = Config::Get('bfacp.metabans.account', FALSE);

        if($this->key === FALSE || $this->user === FALSE || $this->account === FALSE)
        {
            throw new MetabansException('Metabans settings are not configured. Please update the configuration in the site settings.');
        }

        $this->salt = mt_rand(10000, 99999);
        $this->hash = sha1($this->salt . $this->key);

        $this->auth = [
            'apikey'   => $this->hash,
            'salt'     => $this->salt,
            'username' => $this->user
        ];

        $this->guzzle = $guzzle;
        $this->base62 = $base62;
    }

    /**
     * Assess player
     * @param  string  $game     BF_BC2, MOH_2010, BF_3, MOH_2012, BF_4
     * @param  string  $GUID     Player GUID
     * @param  string  $type     None, Watch, White, Black
     * @param  integer $duration Length of time in seconds ban should be enforced. Defaults to 3 months.
     * @param  string  $reason   Ban Reason - Max 200 chars
     */
    public function assess($game, $GUID, $type, $reason = '', $duration = 7776000)
    {
        $rules = [
            'assessment_length' => 'required|numeric',
            'assessment_type'   => 'required|in:' . implode(',' , $this->assessment_types),
            'game_name'         => 'required|in:' . implode(',' , $this->supported_games),
            'player_uid'        => 'required|regex:/^EA_([0-9A-Z]{32}+)$/',
            'reason'            => 'required|max:200',
        ];

        $data = [
            'assessment_length' => $duration,
            'assessment_type'   => $type,
            'game_name'         => $game,
            'player_uid'        => $GUID,
            'reason'            => $reason
        ];

        if( ! $this->validate($data, $rules))
        {
            return MainHelper::response($this->getErrors(), 'Validation failed.', 'error', 400);
        }

        $assessment = $this->request([
            'mb_assess_player' => $data
        ], TRUE);

        return MainHelper::response($assessment);
    }

    /**
     * Fetchs the assessments for an account
     * @param integer $offset
     */
    public function assessments($offset = 0)
    {
        if( ! is_numeric($offset)) $offset = 0;

        $assessments = $this->request([
            'mbo_assessments' => [
                'account_name' => $this->account,
                'offset' => $offset
            ]
        ]);

        foreach($assessments['assessments'] as $key => $assessment)
        {
            $player_url = sprintf("http://metabans.com/player?i=%s", $this->base62->encode($assessment['player_id']));
            $assessment_url = sprintf("http://metabans.com/assessment?i=%s", $this->base62->encode($assessment['assessment_id']));

            $assessments['assessments'][$key]['player_url'] = $player_url;
            $assessments['assessments'][$key]['assessment_url'] = $assessment_url;
        }

        $assessments = new Collection($assessments);

        return $assessments;
    }

    /**
     * Fetchs the feed for an account
     * @param integer $offset
     */
    public function feed($offset = 0)
    {
        if( ! is_numeric($offset)) $offset = 0;

        $feed = $this->request([
            'mbo_feed' => [
                'account_name' => $this->account,
                'offset' => $offset
            ]
        ]);

        foreach($feed['feed'] as $key => $f)
        {
            $player_url = sprintf("http://metabans.com/player?i=%s", $this->base62->encode($f['player_id']));
            $assessment_url = sprintf("http://metabans.com/assessment?i=%s", $this->base62->encode($f['assessment_id']));

            $feed['feed'][$key]['player_url'] = $player_url;
            $feed['feed'][$key]['assessment_url'] = $assessment_url;
        }

        $feed = new Collection($feed);

        return $feed;
    }

    /**
     * Fetchs the followers for an account
     * @param integer $offset
     */
    public function followers($offset = 0)
    {
        if( ! is_numeric($offset)) $offset = 0;

        $followers = new Collection($this->request([
            'mbo_followers' => [
                'account_name' => $this->account,
                'offset' => $offset
            ]
        ]));

        return MainHelper::response($followers);
    }

    /**
     * Get player aliases
     * @param integer $id Metabans player id
     */
    public function aliases($id = 0)
    {
        $rules = [
            'player_id' => 'required|numeric',
        ];

        $data = [
            'player_id' => $id
        ];

        if( ! $this->validate($data, $rules))
        {
            return MainHelper::response($this->getErrors(), 'Validation failed.', 'error', 400);
        }

        $aliases = new Collection($this->request([
            'mbo_player_aliases' => $data
        ]));

        return MainHelper::response($aliases);
    }

    /**
     * Search for playeers
     * @param string $phrase
     */
    public function search($phrase = '')
    {
        $rules = [
            'phrase' => 'required',
        ];

        $data = [
            'phrase' => trim($phrase)
        ];

        if( ! $this->validate($data, $rules))
        {
            return MainHelper::response($this->getErrors(), 'Validation failed.', 'error', 400);
        }

        $matches = new Collection($this->request([
            'mbo_search' => $data
        ])['matches']);

        return MainHelper::response($matches);
    }

    /**
     * Returns the API Key
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the API Username
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Generates the request
     * @param  array   $requests
     * @param  boolean $auth     Request requires authentication
     * @return [type]            [description]
     */
    private function request(array $requests, $auth = FALSE)
    {
        // Holds the request params
        $parsed = [];

        foreach($requests as $action => $param)
        {
            if(substr($action, 0, 3) == 'mb_')
                $auth = TRUE;

            $parsed[] = array_merge(['action' => $action], $param);
        }

        $request = ['requests' => $parsed];

        if($auth)
            $request = array_merge($request, $this->auth);

        return $this->make($request);
    }

    /**
     * Send request to metabans API
     * @param  array  $payload
     * @return
     */
    private function make(array $payload)
    {
        try {
            $request = $this->guzzle->post(self::URL, [
                'headers' => [
                    'User-Agent' => 'PRoCon Metabans Plugin/1.0.6.0'
                ],
                'body' => $payload
            ]);

            $response = $request->json();

            if(array_key_exists('error', $response['responses'][0]))
            {
                throw new MetabansException(
                    $response['responses'][0]['error']['message'],
                    $response['responses'][0]['error']['code']
                );
            }

            $response = $response['responses'][0];

            if(array_key_exists('data', $response))
                return $response['data'];

            return $response;

        } catch(RequestException $e) {

            if($e->hasResponse()) {
                throw new MetabansException(sprintf("Request encountered an error. %s", $e->getResponse()), 400);
            }

            throw new MetabansException("Could not connect to Metabans API");
        }
    }

    /**
     * Validator
     * @param  array $data
     * @param  array $rules
     * @param  array $messages
     * @return bool
     */
    public function validate($data = [], $rules = [], $messages = [])
    {
        $v = Validator::make($data, $rules, $messages);

        if($v->fails())
        {
            $this->setErrors($v->messages());
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Set error message bag
     *
     * @var Illuminate\Support\MessageBag
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Do we have errors
     */
    public function hasErrors()
    {
        return ! empty($this->errors);
    }
}
