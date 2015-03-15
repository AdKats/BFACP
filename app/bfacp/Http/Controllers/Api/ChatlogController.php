<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use MainHelper;

class ChatlogController extends BaseController
{
    protected $chat;

    protected $server;

    public function __construct(Chat $chat, Server $server)
    {
        $this->chat = $chat;
        $this->server = $server;
    }

    public function getIndex()
    {
        $limit = 100;

        $chat = $this->chat
            ->leftJoin('tbl_server', 'tbl_chatlog.ServerID', '=', 'tbl_server.ServerID')
            ->select('tbl_chatlog.*', 'tbl_server.ServerName')
            ->orderBy('logDate', 'desc');

        if( Input::has('limit') && in_array( Input::get('limit'), range(10, 100, 10) ) ) {
            $limit = Input::get('limit');
        }

        if( Input::has('nospam') && Input::get('nospam') == 1 ) {
            $chat = $chat->excludeSpam();
        }

        if( Input::has('between') ) {
            $between = explode(',', Input::get('between'));

            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $between[0]);

            if(count($between) == 1) {
                $endDate = Carbon::now();
            } else {
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $between[1]);
            }

            if($startDate->gte($endDate)) {
                return MainHelper::response(NULL,
                    sprintf("%s is greater than %s. Please adjust your dates.",
                        $startDate->toDateTimeString(),
                        $endDate->toDateTimeString()
                    ),
                    'error',
                    NULL,
                    FALSE,
                    TRUE
                );
            }

            $chat = $chat->whereBetween('logDate', [
                $startDate->toDateTimeString(),
                $endDate->toDateTimeString()
            ])->paginate($limit);
        } else {
            $chat = $chat->simplePaginate($limit);
        }

        return MainHelper::response($chat, NULL, NULL, NULL, FALSE, TRUE);
    }
}
