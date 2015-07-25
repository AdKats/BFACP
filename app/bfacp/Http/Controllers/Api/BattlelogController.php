<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Libraries\AntiCheat;
use BFACP\Libraries\Battlelog\BattlelogPlayer;

class BattlelogController extends BaseController
{
    /**
     * @param Player $player
     *
     * @return mixed
     */
    public function getWeapons(Player $player)
    {
        $battlelog = new BattlelogPlayer($player);

        return MainHelper::response($battlelog->getWeaponStats(), null, null, null, false, true);
    }

    /**
     * @param Player $player
     *
     * @return mixed
     */
    public function getOverview(Player $player)
    {
        $battlelog = new BattlelogPlayer($player);

        return MainHelper::response($battlelog->getOverviewStats(), null, null, null, false, true);
    }

    /**
     * @param Player $player
     *
     * @return mixed
     */
    public function getVehicles(Player $player)
    {
        $battlelog = new BattlelogPlayer($player);

        return MainHelper::response($battlelog->getVehicleStats(), null, null, null, false, true);
    }

    /**
     * @param Player $player
     *
     * @return mixed
     */
    public function getReports(Player $player)
    {
        $battlelog = new BattlelogPlayer($player);

        return MainHelper::response($battlelog->getBattleReports(), null, null, null, false, true);
    }

    /**
     * @param Player $player
     * @param int    $id
     *
     * @return mixed
     */
    public function getReport(Player $player, $id)
    {
        $battlelog = new BattlelogPlayer($player);

        return MainHelper::response($battlelog->getBattleReport($id), null, null, null, false, true);
    }

    /**
     * @param Player $player
     *
     * @return mixed
     */
    public function getCheatDetection(Player $player)
    {
        $acs = new AntiCheat($player);

        $data = $acs->parse($acs->battlelog->getWeaponStats())->get();

        return MainHelper::response($data, null, null, null, false, true);
    }
}
