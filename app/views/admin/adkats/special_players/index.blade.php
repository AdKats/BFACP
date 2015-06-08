@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <th>ID</th>
                            <th>Game</th>
                            <th>Player</th>
                            <th>Group</th>
                            <th>Created</th>
                            <th>Expires</th>
                        </thead>

                        <tbody>
                            @forelse($players as $player)
                            <tr>
                                <td>{{ link_to_route('admin.adkats.special_players.edit', $player->specialplayer_id, [$player->specialplayer_id]) }}</td>
                                <td>
                                    @if(is_null($player->player))
                                    <span class="label bg-red">N/A</span>
                                    @else
                                    <span class="{{ $player->player->game->class_css }}">{{ $player->player->game->Name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(is_null($player->player))
                                    {{ link_to_route('player.listing', $player->player_identifier, ['player' => $player->player_identifier], ['target' => '_blank']) }}
                                    @else
                                    {{ link_to_route('player.show', $player->player->SoldierName, [$player->player->PlayerID, $player->player->SoldierName]) }}
                                    @endif
                                </td>
                                <td>{{ $player->group->group_name }}</td>
                                <td>
                                    <span ng-bind="moment('{{ $player->effective_stamp }}').fromNow()" tooltip="<?php echo '{{';?> moment('<?php echo $player->effective_stamp;?>').format('lll') <?php echo '}}';?>"></span>
                                </td>
                                <td>
                                    <span ng-bind="moment('{{ $player->expiration_stamp }}').fromNow()" tooltip="<?php echo '{{';?> moment('<?php echo $player->expiration_stamp;?>').format('lll') <?php echo '}}';?>"></span>
                                </td>
                            </tr>
                            @empty
                            <alert type="info">
                                {{ HTML::faicon('fa-info') }}
                                {{ Lang::get('adkats.users.no_users') }}
                            </alert>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
