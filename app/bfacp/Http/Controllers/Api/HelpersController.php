<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Main as MainHelper;

class HelpersController extends BaseController
{
    public function getSpecialGroups()
    {
        $groups = MainHelper::specialGroups();

        return MainHelper::response($groups, null, null, null, false, true);
    }
}
