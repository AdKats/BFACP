<?php

use BFACP\Account\Permission;

$adminPermsList = Cache::remember('admin.perm.list', 60 * 24, function () {
    $temp = [];
    foreach (Permission::all() as $permission) {
        if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {
            $temp[$matches[1]][] = $permission->name;
            $temp['_admin'][] = $permission->name;
        }
    }
    return $temp;
});

Menu::make('MainNav', function ($menu) use($adminPermsList) {
    $menu->raw(strtoupper(Lang::get('navigation.main.title')), ['class' => 'header']);
    $menu->add(Lang::get('navigation.main.items.dashboard.title'), ['route' => 'home'])
         ->prepend(HTML::faicon(Lang::get('navigation.main.items.dashboard.icon.fa'), true));
    $menu->add(Lang::get('navigation.main.items.scoreboard.title'), ['route' => 'servers.live'])
         ->prepend(HTML::faicon(Lang::get('navigation.main.items.scoreboard.icon.fa'), true));
    $menu->add(Lang::get('navigation.main.items.playerlist.title'), ['route' => 'player.listing'])
         ->prepend(HTML::faicon(Lang::get('navigation.main.items.playerlist.icon.fa'), true));

    // If the role can access the chatlogs we can add the item to the navigation list
    if ((Auth::check() && Auth::user()->ability(null, 'chatlogs')) || Config::get('bfacp.site.chatlogs.guest')) {
        $menu->add(Lang::get('navigation.main.items.chatlogs.title'), ['route' => 'chatlog.search'])
             ->prepend(HTML::faicon(Lang::get('navigation.main.items.chatlogs.icon.fa'), true));
    }

    // Only show these if the user is logged in
    if (Auth::check()) {

        /*===============================================
        =            AdKats Admin Navigation            =
        ===============================================*/

        if (Auth::user()->ability(null, $adminPermsList['adkats'])) {
            $adkats = $menu->raw(strtoupper(Lang::get('navigation.admin.adkats.title')));

            if (Auth::user()->ability(null, 'admin.adkats.bans.view')) {
                $adkats->add(Lang::get('navigation.admin.adkats.items.banlist.title'), ['route' => 'admin.adkats.bans.index'])
                       ->prepend(HTML::ionicon(Lang::get('navigation.admin.adkats.items.banlist.icon.ion'), true));
            }

            if (Auth::user()->ability(null, 'admin.adkats.user.view')) {
                $adkats->add(Lang::get('navigation.admin.adkats.items.users.title'), ['route' => 'admin.adkats.users.index'])
                       ->prepend(HTML::faicon(Lang::get('navigation.admin.adkats.items.users.icon.fa'), true));
            }
        }

        /*=============================================
        =            Site Admin Navigation            =
        =============================================*/

        if (Auth::user()->ability(null, $adminPermsList['site'])) {
            $site = $menu->raw(strtoupper(Lang::get('navigation.admin.title')), ['class' => 'header']);
        }
    }
});
