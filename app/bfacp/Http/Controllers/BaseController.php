<?php namespace BFACP\Http\Controllers;

use BFACP\Account\Permission;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Menu;

class BaseController extends Controller
{
    public $user;
    public $isLoggedIn;

    private $adminPermsList = [];

    public function __construct()
    {
        $bfacp            = \App::make('bfadmincp');
        $this->user       = $bfacp->user;
        $this->isLoggedIn = $bfacp->isLoggedIn;

        $this->adminPermsList = Cache::remember('admin.perm.list', 60 * 24, function () {
            $temp = [];
            foreach (Permission::all() as $permission) {
                if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {
                    $temp[$matches[1]][] = $permission->name;
                    $temp['_admin'][] = $permission->name;
                }
            }
            return $temp;
        });

        Menu::make('MainNav', function ($menu) {
            $menu->raw(strtoupper(Lang::get('navigation.main.title')), ['class' => 'header']);
            $menu->add(Lang::get('navigation.main.items.dashboard'), ['route' => 'home'])->prepend(HTML::faicon('fa-dashboard', true));
            $menu->add(Lang::get('navigation.main.items.scoreboard'), ['route' => 'servers.live'])->prepend(HTML::faicon('fa-server', true));

            // If the role can access the chatlogs we can add the item to the navigation list
            if (($this->isLoggedIn && $this->user->ability(null, 'chatlogs')) || Config::get('site.chatlogs.guest')) {
                $menu->add(Lang::get('navigation.main.items.chatlogs'), ['route' => 'chatlog.search'])->prepend(HTML::faicon('fa-comments', true));
            }

            // Only show these if the user is logged in
            if ($this->isLoggedIn) {

                /*===============================================
                =            AdKats Admin Navigation            =
                ===============================================*/

                if ($this->user->ability(null, $this->adminPermsList['adkats'])) {
                    $menu->raw(strtoupper(Lang::get('navigation.admin.adkats.title')), ['class' => 'header']);

                    if ($this->user->ability(null, 'admin.adkats.bans.view')) {
                        $menu->add(Lang::get('navigation.admin.adkats.items.banlist'), ['route' => 'admin.adkats.bans.index'])->prepend(HTML::ionicon('ion-hammer', true));
                    }
                }

                /*=============================================
                =            Site Admin Navigation            =
                =============================================*/

                if ($this->user->ability(null, $this->adminPermsList['site'])) {
                    $menu->raw(strtoupper(Lang::get('navigation.admin.title')), ['class' => 'header']);
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
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
}
