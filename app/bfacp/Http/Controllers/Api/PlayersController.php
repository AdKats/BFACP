<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Repositories\PlayerRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlayersController extends Controller
{
    use ControllerTrait;

    private $repository;
    public $request;

    public function __construct(PlayerRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    public function index()
    {
        return $this->repository
            ->setopts($this->repository->request->get('opts', []))
            ->getAllPlayers( $this->request->get( 'limit', FALSE ) );
    }

    public function show($id)
    {
        $result = $this->repository
            ->setopts($this->request->get('opts', []))
            ->getPlayerById($id);

        return $this->response->array($result->toArray());
    }
}
