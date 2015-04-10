<?php

Breadcrumbs::register('home', function($b) {
    $b->push(Lang::get('navigation.main.items.dashboard.title'), route('home'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.dashboard.icon.fa'))
    ]);
});

Breadcrumbs::register('servers.live', function($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.scoreboard.title'), route('servers.live'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.scoreboard.icon.fa'))
    ]);
});

Breadcrumbs::register('chatlog.search', function($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.chatlogs.title'), route('chatlog.search'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.chatlogs.icon.fa'))
    ]);
});

Breadcrumbs::register('player.listing', function($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.main.items.playerlist.title'), route('player.listing'), [
        'icon' => HTML::faicon(Lang::get('navigation.main.items.playerlist.icon.fa'))
    ]);
});

Breadcrumbs::register('player.show', function($b, $id, $name) {
    $b->parent('player.listing');
    $b->push($name);
});

Breadcrumbs::register('admin.adkats', function($b) {
    $b->parent('home');
    $b->push(Lang::get('navigation.admin.adkats.title'), null);
});

/*===================================
=            AdKats Bans            =
===================================*/

Breadcrumbs::register('admin.adkats.bans.index', function($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.banlist.title'), route('admin.adkats.bans.index'), [
        'icon' => HTML::ionicon(Lang::get('navigation.admin.adkats.items.banlist.icon.ion'))
    ]);
});

Breadcrumbs::register('admin.adkats.bans.edit', function($b, $id) {
    $b->parent('admin.adkats.bans.index');
    $b->push(Lang::get('navigation.admin.adkats.items.banlist.items.edit.title', ['id' => $id]));
});

/*====================================
=            AdKats Users            =
====================================*/

Breadcrumbs::register('admin.adkats.users.index', function($b) {
    $b->parent('admin.adkats');
    $b->push(Lang::get('navigation.admin.adkats.items.users.title'), route('admin.adkats.users.index'), [
        'icon' => HTML::faicon(Lang::get('navigation.admin.adkats.items.users.icon.fa'))
    ]);
});

Breadcrumbs::register('admin.adkats.users.edit', function($b, $id) {
    $b->parent('admin.adkats.users.index');
    $b->push(Lang::get('navigation.admin.adkats.items.users.items.edit.title', ['id' => $id]));
});
