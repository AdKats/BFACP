<?php namespace BFACP\Composers;

class UserComposer
{
    public function compose($view)
    {
        $view->with('bfacp', app('bfadmincp'));
    }
}
