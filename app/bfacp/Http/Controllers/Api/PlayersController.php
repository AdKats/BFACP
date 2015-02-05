<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Repositories\PlayerRepository;

class PlayersController extends BaseController
{
    private $repository;

    public function __construct(PlayerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $limit = $this->request->get('limit', FALSE);
        $opts = $this->request->get('opts', []);

        return $this->repository->setopts($opts)->getAllPlayers($limit);
    }

    public function show($id)
    {
        $opts = $this->request->get('opts', []);        
        $result = $this->repository->setopts($opts)->getPlayerById($id);

        return $this->response->array($result->toArray());
    }
}
