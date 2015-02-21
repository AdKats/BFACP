<?php namespace BFACP\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
            $menu->raw('MAIN NAVIGATION', ['class' => 'header']);
            $menu->add('Dashboard', ['route' => 'home'])
                ->prepend('<i class="fa fa-dashboard"></i>')
                ->link->attr(['target' => '_self']);

            $menu->raw('ADMIN NAVIGATION', ['class' => 'header']);

            $menu->add('AdKats Management', 'javascript:://')
                ->add('Locale Editor', ['route' => 'admin.adkats.locale.index'])
                ->link->attr(['target' => '_self']);
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
