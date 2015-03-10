<?php namespace BFACP\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Lavary\Menu\Facade AS Menu;

class BaseController extends Controller
{
    public $user;
    public $isLoggedIn;

    public function __construct()
    {
        Menu::make('MainNav', function($menu)
        {
            $menu->raw(strtoupper(Lang::get('navigation.main.title')), ['class' => 'header']);
            $menu->add(Lang::get('navigation.main.items.dashboard'), ['route' => 'home'])
                ->prepend('<i class="fa fa-dashboard"></i>');

            $menu->raw(strtoupper(Lang::get('navigation.admin.title')), ['class' => 'header']);

            $menu->add(Lang::get('navigation.admin.adkats.title'), 'javascript:://')
                ->add(Lang::get('navigation.admin.adkats.items.locale_editor'), ['route' => 'admin.adkats.locale.index']);
        });

        $this->user = \App::make('bfadmincp')->user;
        $this->isLoggedIn = \App::make('bfadmincp')->isLoggedIn;
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout))
        {
            $this->layout = View::make($this->layout);
        }
    }
}
