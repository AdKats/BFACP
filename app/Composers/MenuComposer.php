<?php

namespace BFACP\Composers;

/**
 * Class MenuComposer.
 */
class MenuComposer
{
    public function compose()
    {
        require_once app_path('menu.php');
    }
}
