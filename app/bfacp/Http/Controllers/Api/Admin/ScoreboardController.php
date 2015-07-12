<?php namespace BFACP\Http\Controllers\Api\Admin;

use BFACP\Battlefield\Server as Server;
use BFACP\Exceptions\PlayerNotFoundException as PlayerNotFoundException;
use BFACP\Exceptions\RconException as RconException;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Input as Input;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException as AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;

class ScoreboardController extends BaseController
{
    const COMPLETE_WITH_ERRORS = 'Completed with errors';

    /**
     * \BFACP\Repositories\Scoreboard\LiveServerRepository
     * @var null
     */
    protected $repository = null;

    /**
     * \BFACP\Battlefield\Server
     * @var null
     */
    protected $server = null;

    /**
     * List of player names
     * @var array
     */
    protected $players = [];

    /**
     * Errors list
     * @var array
     */
    protected $errors = [];

    public function __construct()
    {
        parent::__construct();

        $permissions = Cache::get('admin.perm.list');

        if (!$this->isLoggedIn || !$this->user->ability(null, $permissions['scoreboard'])) {
            throw new AccessDeniedHttpException();
        }

        $id = Input::get('server_id');

        if (!is_numeric($id) || !filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            throw new NotFoundHttpException('Invalid Server ID');
        }

        try {
            $this->server = Server::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('No server found with id of %s.', $id));
        }

        $this->repository = App::make('BFACP\Repositories\Scoreboard\LiveServerRepository', [$this->server])->attempt();

        if (Input::has('players')) {
            $this->players = explode(',', Input::get('players'));
        }
    }

    /**
     * Index
     */
    public function anyIndex()
    {
        return $this->_response(null, 'Resource Not Found', 'error', 404);
    }

    /**
     * Sends a yell to the entire server, team, or selected player(s).
     */
    public function anyYell()
    {
        $this->hasPermission('admin.scoreboard.yell');

        if (Input::get('type') == 'Player' && !empty($this->players)) {
            foreach ($this->players as $player) {
                try {
                    $this->repository->adminYell(
                        Input::get('message', null),
                        $player,
                        null,
                        Input::get('duration', 5),
                        'Player'
                    );
                } catch (PlayerNotFoundException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        } else {
            $this->repository->adminYell(
                Input::get('message', null),
                null,
                Input::get('team', null),
                Input::get('duration', 5),
                Input::get('type', 'All')
            );
        }

        return $this->_response();
    }

    /**
     * Sends a say to the entire server, team, or selected player(s).
     */
    public function anySay()
    {
        $this->hasPermission('admin.scoreboard.say');

        if (Input::get('type') == 'Player' && !empty($this->players)) {
            foreach ($this->players as $player) {
                try {
                    $this->repository->adminSay(
                        Input::get('message', null),
                        $player,
                        null,
                        'Player'
                    );
                } catch (PlayerNotFoundException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        } else {
            $this->repository->adminSay(
                Input::get('message', null),
                null,
                Input::get('team', null),
                Input::get('type', 'All')
            );
        }

        return $this->_response();
    }

    /**
     * Sends a tell to selected player(s).
     */
    public function anyTell()
    {
        $this->hasPermission('admin.scoreboard.say');
        $this->hasPermission('admin.scoreboard.yell');

        foreach ($this->players as $player) {
            try {
                $this->repository->adminTell(
                    $player,
                    Input::get('message', null)
                );
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        return $this->_response();
    }

    /**
     * Kill the selected player(s).
     */
    public function anyKill()
    {
        $this->hasPermission('admin.scoreboard.kill');

        foreach ($this->players as $player) {
            try {
                $this->repository->adminKill(
                    $player,
                    Input::get('message', null)
                );
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player' => $player,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Kick the selected player(s).
     */
    public function anyKick()
    {
        $this->hasPermission('admin.scoreboard.kick');

        foreach ($this->players as $player) {
            try {
                $this->repository->adminKick(
                    $player,
                    Input::get('message', null)
                );
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player' => $player,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Move the selected player(s).
     */
    public function anyTeamswitch()
    {
        $this->hasPermission('admin.scoreboard.teamswitch');

        foreach ($this->players as $player) {
            try {
                $this->repository->adminMovePlayer(
                    $player,
                    Input::get('team', null),
                    Input::get('squad', null),
                    Input::get('locked', false)
                );
            } catch (PlayerNotFoundException $e) {
                $this->errors[] = [
                    'player' => $player,
                    'message' => $e->getMessage()
                ];
            } catch (RconException $e) {
                $this->errors[] = [
                    'player' => $player,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $this->_response();
    }

    /**
     * Wrapper for \BFACP\Facades\Main
     * @param  array $data
     * @param  string $message
     * @param  string $type
     * @return \BFACP\Facades\Main
     */
    private function _response($data = null, $message = null, $type = null)
    {
        if (!empty($this->errors)) {
            return MainHelper::response($this->errors, self::COMPLETE_WITH_ERRORS, null, null, false, true);
        }

        return MainHelper::response($data, $message, $type, null, false, true);
    }

    /**
     * Quick function for checking permissions for the scoreboard admin.
     * @param  string $permission Name of the permission
     * @param  string $message
     * @return boolean
     */
    private function hasPermission(
        $permission,
        $message = 'Access Denied! You do have permission to issue this command.'
    ) {
        if (!$this->user->ability(null, $permission)) {
            throw new AccessDeniedHttpException($message);
        }

        return true;
    }
}
