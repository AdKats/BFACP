<?php

namespace BFACP\Http\Controllers\Api\Admin;

use BFACP\Battlefield\Server\Server as Server;
use BFACP\Exceptions\PlayerNotFoundException as PlayerNotFoundException;
use BFACP\Exceptions\RconException as RconException;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Api\Controller;
use BFACP\Repositories\Scoreboard\LiveServerRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException as AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;

/**
 * Class ScoreboardController.
 */
class ScoreboardController extends Controller
{
    const COMPLETE_WITH_ERRORS = 'Completed with errors';

    /**
     * \BFACP\Repositories\Scoreboard\LiveServerRepository.
     *
     * @var null
     */
    protected $repository = null;

    /**
     * \BFACP\Battlefield\Server\Server.
     *
     * @var null
     */
    protected $server = null;

    /**
     * List of player names.
     *
     * @var array
     */
    protected $players = [];

    /**
     * Data to be passed to response.
     *
     * @var array
     */
    protected $data = [];

    public function __construct()
    {
        parent::__construct();

        $permissions = $this->cache->get('admin.perm.list');

        if (! $this->isLoggedIn || ! $this->user->ability(null, $permissions['scoreboard'])) {
            throw new AccessDeniedHttpException();
        }

        $id = $this->request->get('server_id');

        if (! is_numeric($id) || ! filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            throw new NotFoundHttpException('Invalid Server ID');
        }

        try {
            $this->server = Server::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('No server found with id of %s.', $id));
        }

        $this->repository = app(LiveServerRepository::class, [$this->server])->attempt();

        if ($this->request->has('players')) {
            $this->players = array_map('trim', explode(',', $this->request->get('players')));
        }
    }

    /**
     * Index.
     *
     * @return MainHelper
     */
    public function anyIndex()
    {
        return $this->_response(null, 'Resource Not Found', 'error');
    }

    /**
     * Wrapper for \BFACP\Facades\Main.
     *
     * @param array  $data
     * @param string $message
     * @param string $type
     *
     * @return MainHelper
     */
    private function _response($data = null, $message = null, $type = null)
    {
        $data = [
            'failed' => $this->errors,
            'passed' => $this->data,
            'other'  => $data,
        ];

        if (! empty($this->errors)) {
            $message = self::COMPLETE_WITH_ERRORS;
        }

        return MainHelper::response($data, $message, $type, null, false, true);
    }

    /**
     * Quick function for checking permissions for the scoreboard admin.
     *
     * @param string $permission Name of the permission
     * @param string $message
     *
     * @return bool
     */
    private function hasPermission(
        $permission,
        $message = 'Access Denied! You do have permission to issue this command.'
    ) {
        if (! $this->user->ability(null, $permission)) {
            throw new AccessDeniedHttpException($message);
        }

        return true;
    }

    /**
     * Sends a yell to the entire server, team, or selected player(s).
     *
     * @return MainHelper
     */
    public function postYell()
    {
        $this->hasPermission('admin.scoreboard.yell');

        $message = $this->request->get('message', null);
        $duration = $this->request->get('duration', 5);
        $type = $this->request->get('type', 'All');
        $team = $this->request->get('team', null);

        if ($this->request->get('type') == 'Player' && ! empty($this->players)) {
            foreach ($this->players as $player) {
                try {
                    $this->data[] = [
                        'player'  => $player,
                        'message' => $message,
                        'record'  => $this->repository->adminYell($message, $player, null, $duration, 'Player'),
                    ];
                } catch (PlayerNotFoundException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        } else {
            $this->data[] = [
                'player'  => null,
                'message' => $message,
                'record'  => $this->repository->adminYell($message, null, $team, $duration, $type),
            ];
        }

        return $this->_response();
    }

    /**
     * Issues a ban on the selected players. Ban can be either perm or temp.
     *
     * @return MainHelper
     */
    public function postBan()
    {
        switch($this->request->get('type')) {
            case 'perm':
                $this->hasPermission('admin.scoreboard.pban');
                break;
            case 'temp':
                $this->hasPermission('admin.scoreboard.tban');
                break;
            default:
                throw new RconException(sprintf('Invalid type: %s', $this->request->get('type')));
        }

        return $this->_response();
    }

    /**
     * Sends a say to the entire server, team, or selected player(s).
     *
     * @return MainHelper
     */
    public function postSay()
    {
        $this->hasPermission('admin.scoreboard.say');

        $message = $this->request->get('message', null);
        $team = $this->request->get('team', null);
        $type = $this->request->get('type', 'All');

        if ((bool) $this->request->get('hideName', false) == true) {
            $hideName = false;
        } else {
            $hideName = true;
        }

        if ($this->request->get('type') == 'Player' && ! empty($this->players)) {
            foreach ($this->players as $player) {
                try {
                    $this->data[] = [
                        'player'  => $player,
                        'message' => $message,
                        'record'  => $this->repository->adminSay($message, $player, null, 'Player', $hideName),
                    ];
                } catch (PlayerNotFoundException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        } else {
            $this->data[] = [
                'player'  => null,
                'message' => $message,
                'record'  => $this->repository->adminSay($message, null, $team, $type, $hideName),
            ];
        }

        return $this->_response();
    }

    /**
     * Sends a tell to selected player(s).
     *
     * @return MainHelper
     */
    public function postTell()
    {
        $this->hasPermission('admin.scoreboard.tell');

        foreach ($this->players as $player) {
            try {
                $this->repository->adminTell($player, $this->request->get('message', null), 10, false);
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        return $this->_response();
    }

    /**
     * Kill the selected player(s).
     *
     * @return MainHelper
     */
    public function postKill()
    {
        $this->hasPermission('admin.scoreboard.kill');

        foreach ($this->players as $player) {
            try {
                $this->data[] = $this->repository->adminKill($player, $this->request->get('message', null));
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Nukes a targeted team.
     *
     * @return MainHelper
     */
    public function postNuke()
    {
        $this->hasPermission('admin.scoreboard.nuke');

        if ($this->request->has('teamId')) {
            $teamId = $this->request->get('teamId', 0);
        } else {
            return $this->_response(null, 'Team ID Required', 'error');
        }

        if (! is_numeric($teamId)) {
            return $this->_response(null, 'Invalid Team ID', 'error');
        }

        $this->repository->adminNuke((int) $teamId);

        return $this->_response();
    }

    /**
     * Kick the selected player(s).
     *
     * @return MainHelper
     */
    public function postKick()
    {
        $this->hasPermission('admin.scoreboard.kick');

        foreach ($this->players as $player) {
            try {
                $this->data[] = $this->repository->adminKick($player, $this->request->get('message', null));
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Move the selected player(s).
     *
     * @return MainHelper
     */
    public function postTeamswitch()
    {
        $this->hasPermission('admin.scoreboard.teamswitch');

        foreach ($this->players as $player) {
            try {
                $team = $this->request->get('team', null);
                $squad = $this->request->get('squad', null);
                $locked = $this->request->get('locked', false);

                $this->data[] = $this->repository->adminMovePlayer($player, $team, $squad, $locked);
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            } catch (RconException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Punish the selected player(s).
     *
     * @return MainHelper
     */
    public function postPunish()
    {
        $this->hasPermission('admin.scoreboard.punish');

        foreach ($this->players as $player) {
            try {
                $this->data[] = $this->repository->adminPunish($player, $this->request->get('message', null));
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            } catch (RconException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Forgive the selected player(s).
     *
     * @return MainHelper
     */
    public function postForgive()
    {
        $this->hasPermission('admin.scoreboard.forgive');

        foreach ($this->players as $player) {
            try {
                $this->data[] = $this->repository->adminForgive($player, $this->request->get('message', null));
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            } catch (RconException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Mute the selected player(s).
     *
     * @return MainHelper
     */
    public function postMute()
    {
        $this->hasPermission('admin.scoreboard.mute');

        foreach ($this->players as $player) {
            try {
                $this->data[] = $this->repository->adminMute($player, $this->request->get('message', null));
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            } catch (RconException $e) {
                $this->errors[] = [
                    'player'  => $player,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $this->_response();
    }
}
