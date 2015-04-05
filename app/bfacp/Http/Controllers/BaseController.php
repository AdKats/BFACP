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
        $this->user = \App::make('bfadmincp')->user;
        $this->isLoggedIn = \App::make('bfadmincp')->isLoggedIn;

        Menu::make('MainNav', function($menu)
        {
            $menu->raw(strtoupper(Lang::get('navigation.main.title')), ['class' => 'header']);
            $menu->add(Lang::get('navigation.main.items.dashboard'), ['route' => 'home'])->prepend(HTML::ficon('fa-dashboard'));

            if(!$this->isLoggedIn) {
                $menu->raw(strtoupper(Lang::get('navigation.admin.title')), ['class' => 'header']);

                if(Entrust::can('admin.adkats.bans.view')) {
                    $menu->add(Lang::get('navigation.main.items.banlist'), ['route' => 'admin.adkats.bans.index'])->prepend(HTML::ficon('fa-hammer'));
                }
            }
        });
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
