<?php

use BFACP\Account\Role;
use BFACP\Battlefield\Server\Server;

Breadcrumbs::register('home', function ($b) {
    $b->push(Lang::get('navigation.main.items.dashboard.title'), route('home'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.dashboard.icon.fa')),
    ]);
});

Breadcrumbs::register('servers.live', function ($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.scoreboard.title'), route('servers.live'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.scoreboard.icon.fa')),
    ]);
});

Breadcrumbs::register('chatlog.search', function ($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.chatlogs.title'), route('chatlog.search'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.chatlogs.icon.fa')),
    ]);
});

Breadcrumbs::register('player.listing', function ($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.playerlist.title'), route('player.listing'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.playerlist.icon.fa')),
    ]);
});

Breadcrumbs::register('player.show', function ($b, $id, $name = null) {
    $b->parent('player.listing');

    if (empty($name)) {
        if (Cache::has(sprintf('player.%u', $id))) {
            $player = Cache::get(sprintf('player.%u', $id));
            $b->push($player->SoldierName);
        } else {
            $b->push(sprintf('#%u', $id));
        }
    } else {
        $b->push($name);
    }
});

Breadcrumbs::register('admin.adkats', function ($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.admin.adkats.title'), null);
});

Breadcrumbs::register('admin.site', function ($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.admin.site.title'), null);
});

/*===================================
=            Adkats Bans            =
===================================*/

Breadcrumbs::register('admin.adkats.bans.index', function ($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.banlist.title'), route('admin.adkats.bans.index'), [
        'icon' => HTML::ionicon(Lang::get('navigation.admin.adkats.items.banlist.icon.ion')),
    ]);
});

Breadcrumbs::register('admin.adkats.bans.edit', function ($b, $id) {
    $b->parent('admin.adkats.bans.index');
    $b->push(Lang::get('navigation.admin.adkats.items.banlist.items.edit.title', ['id' => $id]));
});

/*====================================
=            Adkats Users            =
====================================*/

Breadcrumbs::register('admin.adkats.users.index', function ($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.users.title'), route('admin.adkats.users.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.adkats.items.users.icon.fa')),
    ]);
});

Breadcrumbs::register('admin.adkats.users.edit', function ($b, $id) {
    $b->parent('admin.adkats.users.index');
    $b->push(Lang::get('navigation.admin.adkats.items.users.items.edit.title', ['id' => $id]));
});

/*====================================
=            Adkats Roles            =
====================================*/

Breadcrumbs::register('admin.adkats.roles.index', function ($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.roles.title'), route('admin.adkats.roles.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.adkats.items.roles.icon.fa')),
    ]);
});

Breadcrumbs::register('admin.adkats.roles.edit', function ($b) {
    $b->parent('admin.adkats.roles.index');
    $b->push(Lang::get('navigation.admin.adkats.items.roles.items.edit.title'));
});

Breadcrumbs::register('admin.adkats.roles.create', function ($b) {
    $b->parent('admin.adkats.roles.index');
    $b->push(Lang::get('navigation.admin.adkats.items.roles.items.create.title'));
});

/*==============================================
=            Adkats Special Players            =
==============================================*/

Breadcrumbs::register('admin.adkats.special_players.index', function ($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.special_players.title'),
        route('admin.adkats.special_players.index'), [
            'icon' => HTML::faicon(Lang::get('navigation.admin.adkats.items.special_players.icon.fa')),
        ]);
});

/*==================================
=            Site Users            =
==================================*/

Breadcrumbs::register('admin.site.users.index', function ($b) {
    $b->parent('admin.site');
    $b->push(Lang::get('navigation.admin.site.items.users.title'), route('admin.site.users.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.site.items.users.icon.fa')),
    ]);
});

Breadcrumbs::register('admin.site.users.edit', function ($b, $id) {
    $b->parent('admin.site.users.index');
    $b->push(Lang::get('navigation.admin.site.items.users.items.edit.title', ['id' => $id]));
});

/*==================================
=            Site Roles            =
==================================*/

Breadcrumbs::register('admin.site.roles.index', function ($b) {
    $b->parent('admin.site');
    $b->push(Lang::get('navigation.admin.site.items.roles.title'), route('admin.site.roles.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.site.items.roles.icon.fa')),
    ]);
});

Breadcrumbs::register('admin.site.roles.edit', function ($b, $id) {
    $b->parent('admin.site.roles.index');
    $b->push(Lang::get('navigation.admin.site.items.roles.items.edit.title', ['name' => Role::find($id)->name]));
});

/*=====================================
=            Site Settings            =
=====================================*/

Breadcrumbs::register('admin.site.settings.index', function ($b) {
    $b->parent('admin.site');
    $b->push(Lang::get('navigation.admin.site.items.settings.title'), route('admin.site.settings.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.site.items.settings.icon.fa')),
    ]);
});

/*====================================
=            Site Updater            =
====================================*/

Breadcrumbs::register('admin.updater.index', function ($b) {
    $b->parent('admin.site');
    $b->push(Lang::get('navigation.admin.site.items.updater.title'), route('admin.updater.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.site.items.updater.icon.fa')),
    ]);
});

/*====================================
=            Site Servers            =
====================================*/

Breadcrumbs::register('admin.site.servers.index', function ($b) {
    $b->parent('admin.site');
    $b->push(Lang::get('navigation.admin.site.items.servers.title'), route('admin.site.servers.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.site.items.servers.icon.fa')),
    ]);
});

Breadcrumbs::register('admin.site.servers.edit', function ($b, $id) {
    $b->parent('admin.site.servers.index');
    $b->push(sprintf('%s', Server::find($id)->ServerName));
});
