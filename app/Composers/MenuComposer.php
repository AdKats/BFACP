<?php

namespace BFACP\Composers;

/**
 * Class MenuComposer
 * @package BFACP\Composers
 */
class MenuComposer
{
    public function compose()
    {
        /*================================================
        =            Require the Menu Builder            =
        ================================================*/

        if (! file_exists(app_path('setup.php'))) {
            require_once app_path('menu.php');
        }
    }
}
