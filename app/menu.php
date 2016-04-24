<?php

use BFACP\Account\Permission;
use BFACP\Facades\Macros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

$adminPermsList = Cache::remember('admin.perm.list', 60 * 24, function () {
    $temp = [];
    $permissions = Permission::all();
    $permissions->each(function ($permission) use (&$temp) {
        $permName = (string) $permission->name;
        if (preg_match('/^admin\\.([a-z]+)/A', $permName, $matches)) {
            $temp[$matches[1]][] = $permName;
            $temp['_admin'][] = $permName;
        }
    });

    return $temp;
});

Menu::make('MainNav', function ($menu) use ($adminPermsList) {
    $menu->raw(strtoupper(trans('navigation.main.title')), ['class' => 'header']);

    $menu->add(trans('navigation.main.items.dashboard.title'),
        ['route' => 'home'])->prepend(Macros::faicon(trans('navigation.main.items.dashboard.icon.fa'), true));

    $servers = $menu->raw(trans('navigation.main.items.servers.title'));

    $servers->add(trans('navigation.main.items.servers.list.title'),
        ['route' => 'servers.list'])->prepend(Macros::faicon(trans('navigation.main.items.servers.list.icon.fa'),
        true));

    $servers->add(trans('navigation.main.items.servers.scoreboard.title'),
        ['route' => 'servers.live'])->prepend(Macros::faicon(trans('navigation.main.items.servers.scoreboard.icon.fa'),
        true));

    if (Auth::check() && Auth::user()->ability(null, 'admin.adkats.bans.view')) {
        $menu->add(trans('navigation.admin.adkats.items.banlist.title'),
            ['route' => 'admin.adkats.bans.index'])->prepend(Macros::ionicon(trans('navigation.admin.adkats.items.banlist.icon.ion'),
            true));
    }

    $menu->add(trans('navigation.main.items.playerlist.title'),
        ['route' => 'player.listing'])->prepend(Macros::faicon(trans('navigation.main.items.playerlist.icon.fa'),
        true));

    // If the role can access the chatlogs we can add the item to the navigation list
    if ((Auth::check() && Auth::user()->ability(null, 'chatlogs')) || Config::get('bfacp.site.chatlogs.guest')) {
        $menu->add(trans('navigation.main.items.chatlogs.title'),
            ['route' => 'chatlog.search'])->prepend(Macros::faicon(trans('navigation.main.items.chatlogs.icon.fa'),
            true));
    }

    // Only show these if the user is logged in
    if (Auth::check()) {

        /*===============================================
        =            Adkats Admin Navigation            =
        ===============================================*/

        if (Auth::user()->ability(null, $adminPermsList['adkats'])) {
            $adkats = $menu->raw(trans('navigation.admin.adkats.title'));

            if (Auth::user()->ability(null, 'admin.adkats.user.view')) {
                $adkats->add(trans('navigation.admin.adkats.items.users.title'),
                    ['route' => 'admin.adkats.users.index'])->prepend(Macros::faicon(trans('navigation.admin.adkats.items.users.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.adkats.roles.view')) {
                $adkats->add(trans('navigation.admin.adkats.items.roles.title'),
                    ['route' => 'admin.adkats.roles.index'])->prepend(Macros::faicon(trans('navigation.admin.adkats.items.roles.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.adkats.special.view')) {
                $adkats->add(trans('navigation.admin.adkats.items.special_players.title'),
                    ['route' => 'admin.adkats.special_players.index'])->prepend(Macros::faicon(trans('navigation.admin.adkats.items.special_players.icon.fa'),
                    true));
            }
        }

        /*=============================================
        =            Site Admin Navigation            =
        =============================================*/

        if (Auth::user()->ability(null, $adminPermsList['site'])) {
            $site = $menu->raw(trans('navigation.admin.site.title'));

            if (Auth::user()->ability(null, 'admin.site.users')) {
                $site->add(trans('navigation.admin.site.items.users.title'),
                    ['route' => 'admin.site.users.index'])->prepend(Macros::faicon(trans('navigation.admin.site.items.users.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.site.roles')) {
                $site->add(trans('navigation.admin.site.items.roles.title'),
                    ['route' => 'admin.site.roles.index'])->prepend(Macros::faicon(trans('navigation.admin.site.items.roles.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.site.settings.site')) {
                $site->add(trans('navigation.admin.site.items.settings.title'),
                    ['route' => 'admin.site.settings.index'])->prepend(Macros::faicon(trans('navigation.admin.site.items.settings.icon.fa'),
                    true));

                $site->add(trans('navigation.admin.site.items.updater.title'),
                    ['route' => 'admin.updater.index'])->prepend(Macros::faicon(trans('navigation.admin.site.items.updater.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.site.settings.server')) {
                $site->add(trans('navigation.admin.site.items.servers.title'),
                    ['route' => 'admin.site.servers.index'])->prepend(Macros::faicon(trans('navigation.admin.site.items.servers.icon.fa'),
                    true));
            }

            if (Auth::user()->ability(null, 'admin.site.system.logs')) {
                $site->add(trans('navigation.admin.site.items.system.logs.title'),
                    Config::get('logviewer::base_url'))->prepend(Macros::faicon(trans('navigation.admin.site.items.system.logs.icon.fa'),
                    true));
            }
        }

        $ips = explode('|', env('IP_WHITELIST', ''));
        $clientIP = $_SERVER['REMOTE_ADDR'];

        if (in_array($clientIP, $ips)) {
            $menu->add(trans('navigation.main.items.maintenance.title'),
                ['route' => 'admin.site.maintenance.index'])->prepend(Macros::faicon(trans('navigation.main.items.maintenance.icon.fa'),
                true));
        }
    }
});
