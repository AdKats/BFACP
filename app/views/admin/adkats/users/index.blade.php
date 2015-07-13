@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            @if($bfacp->user->ability(null, 'admin.adkats.user.edit'))
            <div class="box-header">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools pull-right">
                    <button class="btn bg-green" id="create-user">
                        <i class="fa fa-plus"></i>&nbsp;<span>{{ Lang::get('adkats.users.listing.buttons.create') }}</span>
                    </button>
                </div>
            </div>
            @endif
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <th>{{ Lang::get('adkats.users.listing.table.col1') }}</th>
                            <th>{{ Lang::get('adkats.users.listing.table.col2') }}</th>
                            <th>{{ Lang::get('adkats.users.listing.table.col3') }}</th>
                            <th>{{ Lang::get('adkats.users.listing.table.col4') }}</th>
                            <th>{{ Lang::get('adkats.users.listing.table.col5') }}</th>
                            <th>{{ Lang::get('adkats.users.listing.table.col6') }}</th>
                        </thead>

                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ link_to_route('admin.adkats.users.edit', $user->user_name, $user->user_id, ['target' => '_self']) }}</td>
                                <td>{{ $user->user_email or 'N/A' }}</td>
                                <td>{{ $user->role->role_name }}</td>
                                <td><span ng-bind="moment('{{ $user->stamp }}').fromNow()" tooltip="{{ HTML::moment($user->stamp) }}"></span></td>
                                <td>
                                    <ul class="list-inline">
                                        @forelse($user->soldiers as $soldier)
                                        <li>
                                            @if(!is_null($soldier->player->game))
                                            {{ link_to_route('player.show', $soldier->player->game->Name, [
                                                $soldier->player->PlayerID,
                                                $soldier->player->SoldierName
                                            ], [
                                                'target' => '_blank',
                                                'class' => $soldier->player->game->class_css,
                                                'style' => 'color: white !important',
                                                'tooltip' => $soldier->player->SoldierName
                                            ]) }}
                                            @else
                                            {{ $soldier->player->SoldierName }}
                                            @endif
                                        </li>
                                        @empty
                                        <label class="label bg-red">{{ Lang::get('adkats.users.no_soldiers') }}</label>
                                        @endforelse
                                    </ul>
                                </td>
                                <td><span popover-placement="left" popover="{{ $user->user_notes }}" popover-trigger="mouseenter">{{ str_limit($user->user_notes, 60) }}</span></td>
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
@if($bfacp->user->ability(null, 'admin.adkats.user.edit'))
<script type="text/javascript">
    $('#create-user').click(function(e) {
        e.preventDefault();

        var btn = $(this);

        var promptVal = prompt('Enter Username', '');

        if(promptVal === '') {
            alert('Username can\'t be blank.');
            return false;
        }

        if(promptVal !== null) {
            btn.find('i').removeClass('fa-plus').addClass('fa-spinner fa-pulse');
            btn.attr('disabled', true);
            $.ajax({
                url: "{{ route('admin.adkats.users.store') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    username: promptVal
                }
            })
            .done(function(data) {
                window.location.href = data.data.url;
            })
            .fail(function(xhr) {
                var response = xhr.responseJSON;
                alert(response.message);
            })
            .always(function() {
                btn.find('i').removeClass('fa-spinner fa-pulse').addClass('fa-plus');
                btn.attr('disabled', false);
            });
        }
    });
</script>
@endif
@stop
