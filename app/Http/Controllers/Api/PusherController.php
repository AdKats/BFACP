<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Main;
use Carbon\Carbon;

/**
 * Class PusherController.
 */
class PusherController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postAuth()
    {
        if ($this->request->has('channel_name') && $this->request->has('socket_id')) {
            $data = [
                'id'        => $this->user->id,
                'username'  => $this->user->username,
                'avatar'    => $this->user->gravatar,
                'role'      => $this->user->roles[0]->name,
                'timestamp' => Carbon::now()->getTimestamp(),
            ];

            $channel_name = $this->request->get('channel_name');
            $socket_id = $this->request->get('socket_id');

            $socket = pusher()->presence_auth($channel_name, $socket_id, $data['id'], $data);

            return response($socket);
        }

        return response('Forbidden', 403);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatHistory()
    {
        $history = [];

        if ($this->cache->has('site.chat.history')) {
            $history = $this->cache->get('site.chat.history');
        }

        $response = Main::response($history, null, null, null, false, true);

        return response()->json($response);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postChat()
    {
        if ($this->request->has('channel_name') && $this->request->has('event')) {
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
                'text'      => $this->request->get('message'),
            ];

            if ($this->cache->has('site.chat.history')) {
                $history = array_merge($history, $this->cache->pull('site.chat.history'));
            }

            $history[] = $data;

            $this->cache->put('site.chat.history', $history, $timestamp->addMinutes(10));

            $channel_name = $this->request->get('channel_name');
            $event = $this->request->get('event');

            pusher()->trigger($channel_name, $event, $data);

            return response('OK', 200);
        }

        return response('Forbidden', 403);
    }
}
