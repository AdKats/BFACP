<?php

use BFACP\Account\Permission;

$adminPermsList = Cache::remember('admin.perm.list', 60 * 24, function () {
    $temp = [];
    $permissions = Permission::all();
    $permissions->each(function ($permission) use (&$temp) {
        if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {
            $temp[ $matches[1] ][] = $permission->name;
            $temp['_admin'][] = $permission->name;
        }
    });

    return $temp;
});

$bfacp = App::make('bfadmincp');

Menu::make('MainNav', function ($menu) use ($adminPermsList, $bfacp) {
    $menu->raw(strtoupper(Lang::get('navigation.main.title')), ['class' => 'header']);

    $menu->add(Lang::get('navigation.main.items.dashboard.title'), ['route' => 'home'])
        ->prepend(HTML::faicon(Lang::get('navigation.main.items.dashboard.icon.fa'), true));

    $menu->add(Lang::get('navigation.main.items.scoreboard.title'), ['route' => 'servers.live'])
        ->prepend(HTML::faicon(Lang::get('navigation.main.items.scoreboard.icon.fa'), true));

    if ($bfacp->isLoggedIn && $bfacp->user->ability(null, 'admin.adkats.bans.view')) {
        $menu->add(Lang::get('navigation.admin.adkats.items.banlist.title'), ['route' => 'admin.adkats.bans.index'])
            ->prepend(HTML::ionicon(Lang::get('navigation.admin.adkats.items.banlist.icon.ion'), true));
    }

    $menu->add(Lang::get('navigation.main.items.playerlist.title'), ['route' => 'player.listing'])
        ->prepend(HTML::faicon(Lang::get('navigation.main.items.playerlist.icon.fa'), true));

    // If the role can access the chatlogs we can add the item to the navigation list
    if (($bfacp->isLoggedIn && $bfacp->user->ability(null, 'chatlogs')) || Config::get('bfacp.site.chatlogs.guest')) {
        $menu->add(Lang::get('navigation.main.items.chatlogs.title'), ['route' => 'chatlog.search'])
            ->prepend(HTML::faicon(Lang::get('navigation.main.items.chatlogs.icon.fa'), true));
    }

    // Only show these if the user is logged in
    if ($bfacp->isLoggedIn) {

        /*===============================================
        =            Adkats Admin Navigation            =
        ===============================================*/

        if ($bfacp->user->ability(null, $adminPermsList['adkats'])) {
            $adkats = $menu->raw(Lang::get('navigation.admin.adkats.title'));

            if ($bfacp->user->ability(null, 'admin.adkats.user.view')) {
                $adkats->add(Lang::get('navigation.admin.adkats.items.users.title'),
                    ['route' => 'admin.adkats.users.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.adkats.items.users.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.adkats.roles.view')) {
                $adkats->add(Lang::get('navigation.admin.adkats.items.roles.title'),
                    ['route' => 'admin.adkats.roles.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.adkats.items.roles.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.adkats.special.view')) {
                $adkats->add(Lang::get('navigation.admin.adkats.items.special_players.title'),
                    ['route' => 'admin.adkats.special_players.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.adkats.items.special_players.icon.fa'), true));
            }
        }

        /*=============================================
        =            Site Admin Navigation            =
        =============================================*/

        if ($bfacp->user->ability(null, $adminPermsList['site'])) {
            $site = $menu->raw(Lang::get('navigation.admin.site.title'));

            if ($bfacp->user->ability(null, 'admin.site.users')) {
                $site->add(Lang::get('navigation.admin.site.items.users.title'), ['route' => 'admin.site.users.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.users.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.site.roles')) {
                $site->add(Lang::get('navigation.admin.site.items.roles.title'), ['route' => 'admin.site.roles.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.roles.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.site.settings.site')) {
                $site->add(Lang::get('navigation.admin.site.items.settings.title'),
                    ['route' => 'admin.site.settings.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.settings.icon.fa'), true));

                $site->add(Lang::get('navigation.admin.site.items.updater.title'), ['route' => 'admin.updater.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.updater.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.site.settings.server')) {
                $site->add(Lang::get('navigation.admin.site.items.servers.title'),
                    ['route' => 'admin.site.servers.index'])
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.servers.icon.fa'), true));
            }

            if ($bfacp->user->ability(null, 'admin.site.system.logs')) {
                $site->add(Lang::get('navigation.admin.site.items.system.logs.title'),
                    Config::get('logviewer::base_url'))
                    ->prepend(HTML::faicon(Lang::get('navigation.admin.site.items.system.logs.icon.fa'), true));
            }
        }

        $clientIp = $_SERVER['REMOTE_ADDR'];
        $whitelist = getenv('IP_WHITELIST') !== false ? explode('|', getenv('IP_WHITELIST')) : [];

        if (in_array($clientIp, $whitelist)) {
            $menu->add(Lang::get('navigation.main.items.maintenance.title'),
                ['route' => 'admin.site.maintenance.index'])
                ->prepend(HTML::faicon(Lang::get('navigation.main.items.maintenance.icon.fa'), true));
        }
    }
});
