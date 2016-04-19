<?php namespace BFACP\Http\Controllers\Api;

use Artdarek\Pusherer\Facades\Pusherer;
use BFACP\Facades\Main;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class PusherController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->beforeFilter('auth');
    }

    /**
     * @return mixed
     */
    public function postAuth()
    {
        if (Input::has('channel_name') && Input::has('socket_id')) {
            $data = [
                'id'        => $this->user->id,
                'username'  => $this->user->username,
                'avatar'    => $this->user->gravatar,
                'role'      => $this->user->roles[0]->name,
                'timestamp' => Carbon::now()->getTimestamp(),
            ];

            $channel_name = Input::get('channel_name');
            $socket_id = Input::get('socket_id');

            $socket = Pusherer::presence_auth($channel_name, $socket_id, $data['id'], $data);

            return \Response::make($socket);
        }

        return \Response::make('Forbidden', 403);
    }

    public function getChatHistory()
    {
        $history = [];

        if (Cache::has('site.chat.history')) {
            $history = Cache::get('site.chat.history');
        }

        return Main::response($history, null, null, null, false, true);
    }

    /**
     * @return mixed
     */
    public function postChat()
    {
        if (Input::has('channel_name') && Input::has('event')) {
            $timestamp = Carbon::now();

            $history = [];

            $data = [
                'hash'      => md5(sprintf('%s_%s', $timestamp->getTimestamp(), $this->user->id)),
                'user'      => [
                    'id'       => $this->user->id,
                    'username' => $this->user->username,
                    'avatar'   => $this->user->gravatar,
                    'role'     => $this->user->roles[0]->name,
                ],
                'timestamp' => $timestamp->toIso8601String(),
                'text'      => Input::get('message'),
            ];

            if (Cache::has('site.chat.history')) {
                $history = array_merge($history, Cache::pull('site.chat.history'));
            }

            $history[] = $data;

            Cache::put('site.chat.history', $history, $timestamp->addMinutes(10));

            $channel_name = Input::get('channel_name');
            $event = Input::get('event');

            Pusherer::trigger($channel_name, $event, $data);

            return \Response::make('OK', 200);
        }

        return \Response::make('Forbidden', 403);
    }
}
