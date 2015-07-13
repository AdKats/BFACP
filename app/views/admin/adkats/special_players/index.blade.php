@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <th width="50px">{{ Lang::get('adkats.special_players.listing.table.col1') }}</th>
                            <th width="50px">{{ Lang::get('adkats.special_players.listing.table.col2') }}</th>
                            <th width="250px">{{ Lang::get('adkats.special_players.listing.table.col3') }}</th>
                            <th width="500px">{{ Lang::get('adkats.special_players.listing.table.col4') }}</th>
                            <th>{{ Lang::get('adkats.special_players.listing.table.col5') }}</th>
                            <th>{{ Lang::get('adkats.special_players.listing.table.col6') }}</th>
                        </thead>

                        <tbody>
                            @forelse($players as $player)
                            <tr>
                                <td>{{ $player->specialplayer_id }}</td>
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
                                    {{ link_to_route('player.show', $player->player->SoldierName, [$player->player->PlayerID, $player->player->SoldierName], ['target' => '_self']) }}
                                    @endif
                                </td>
                                <td>{{ Former::select('group')->fromQuery($groups, 'group_name', 'group_key')->value($player->player_group)->data_special_id($player->specialplayer_id) }}</td>
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

@section('scripts')
<script type="text/javascript">
    $('select[name="group"]').change(function() {
        var _this = $(this);
        var special_id = _this.data('special-id');
        var request_url = 'admin/adkats/special_players/' + special_id;
        _this.attr('disabled', true);

        $.ajax({
            url: request_url,
            type: 'PUT',
            dataType: 'json',
            data: {
                group: $(this).val()
            },
        })
        .done(function(data) {
            toastr.success(data.message);
        })
        .fail(function(data) {
            toastr.error(data.responseJSON.message);
        })
        .always(function() {
            _this.attr('disabled', false);
        });
    });
</script>
@stop
