<?php namespace BFACP\Composers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserComposer
{
    public function compose(View $view)
    {
        $app = new \stdClass();

        $app->isLoggedIn = Auth::check();
        $app->user = null;

        if ($app->isLoggedIn) {
            $app->user = Auth::user();
            App::setLocale($app->user->setting->lang);
        }

        $view->with('bfacp', $app);
    }
}
