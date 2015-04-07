<?php namespace BFACP\Http\Controllers;

use BFACP\Account\Permission;
use Entrust;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
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
            $menu->add(Lang::get('navigation.main.items.dashboard'), ['route' => 'home'])->prepend(HTML::faicon('fa-dashboard'));

            if ($this->isLoggedIn) {

                /**
                 * AdKats Section
                 */
                if ($this->user->ability(null, $this->adminPermsList['_admin'])) {
                    $menu->raw(strtoupper(Lang::get('navigation.admin.title')), ['class' => 'header']);
                }

                if ($this->user->ability(null, $this->adminPermsList['adkats'])) {
                    if (Entrust::can('admin.adkats.bans.view')) {
                        $menu->add(Lang::get('navigation.admin.adkats.items.banlist'), ['route' => 'admin.adkats.bans.index'])->prepend(HTML::ionicon('ion-hammer'));
                    }
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
