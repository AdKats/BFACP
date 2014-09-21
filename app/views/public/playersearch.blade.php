@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <div class="box-tools">
                    <div class="pull-right">
                        <a href="{{Request::url() . '?' . $querystring}}" class="btn btn-default btn-xs {{ (!Input::has('only') ? 'active' : '') }}">Show All</a>
                        <a href="{{Request::url() . '?' . $querystring}}&only=bf3" class="btn btn-default btn-xs {{ (Input::has('only') && Input::get('only') == 'bf3' ? 'active' : '') }}">Show BF3 Only</a>
                        <a href="{{Request::url() . '?' . $querystring}}&only=bf4" class="btn btn-default btn-xs {{ (Input::has('only') && Input::get('only') == 'bf4' ? 'active' : '') }}">Show BF4 Only</a>
                    </div>
                </div>
            </div>
            <div class="box-body">

                <table class="table table-condensed">
                    <thead>
                        <th>ID
                            <a href="{{Request::url()}}<?php if(is_null($urlbuild)) echo "?sort=PlayerID&order=desc&player=".Input::get('player'). (Input::has('page') ? '&page='.Input::get('page') : NULL).""; ?>{{(!is_null($urlbuild) ? $urlbuild . '&sort=PlayerID' : '')}}">
                                <i class="fa {{ strtolower(Input::get('order')) != 'asc' && Input::get('sort') == 'PlayerID' ? 'fa-sort-asc' : 'fa-sort-desc'}}"></i>
                            </a>
                        </th>
                        <th>Player
                            <a href="{{Request::url()}}<?php if(is_null($urlbuild)) echo "?sort=SoldierName&order=desc&player=".Input::get('player').""; ?>{{(!is_null($urlbuild) ? $urlbuild . '&sort=SoldierName' : '')}}">
                                <i class="fa {{ strtolower(Input::get('order')) != 'asc' && Input::get('sort') == 'SoldierName' ? 'fa-sort-asc' : 'fa-sort-desc'}}"></i>
                            </a>
                        </th>
                        <th>Game
                            <a href="{{Request::url()}}<?php if(is_null($urlbuild)) echo "?sort=GameName&order=desc&player=".Input::get('player').""; ?>{{(!is_null($urlbuild) ? $urlbuild . '&sort=GameName' : '')}}">
                                <i class="fa {{ strtolower(Input::get('order')) != 'asc' && Input::get('sort') == 'GameName' ? 'fa-sort-asc' : 'fa-sort-desc'}}"></i>
                            </a>
                        </th>
                    </thead>
                    <tbody>
                        @foreach($results as $player)
                        <tr>
                            <td>{{ $player['PlayerID'] }}</td>
                            <td>{{ $player['SoldierName'] }}</td>
                            <td>{{ $player['GameName'] }}</td>
                            <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', 'Player Profile', [$player['PlayerID'], $player['SoldierName']], ['class' => 'btn btn-primary btn-xs']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <div class="pull-left">
                    Showing {{$results->getFrom()}} to {{$results->getTo()}} of {{$results->getTotal()}}
                </div>
                <div class="pull-right">
                    @if(Input::has('sort') && Input::has('order'))
                        @if(Input::has('only'))
                        {{ $results->appends(array('player' => Input::get('player'), 'sort' => Input::get('sort'), 'order' => Input::get('order'), 'only' => Input::get('only')))->links() }}
                        @else
                        {{ $results->appends(array('player' => Input::get('player'), 'sort' => Input::get('sort'), 'order' => Input::get('order')))->links() }}
                        @endif
                    @else
                        @if(Input::has('only'))
                        {{ $results->appends(array('player' => Input::get('player'), 'only' => Input::get('only')))->links() }}
                        @else
                        {{ $results->appends(array('player' => Input::get('player')))->links() }}
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@stop
