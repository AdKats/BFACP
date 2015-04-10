@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
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
                                <td>{{ link_to_route('admin.adkats.users.edit', $user->user_name, [$user->user_id]) }}</td>
                                <td>{{ $user->user_email or 'N/A' }}</td>
                                <td>{{ $user->role->role_name }}</td>
                                <td><span ng-bind="moment('{{ $user->stamp }}').fromNow()" tooltip="{{ HTML::moment($user->stamp) }}"></span></td>
                                <td>
                                    <ul class="list-inline">
                                        @forelse($user->soldiers as $soldier)
                                        <li>
                                            {{ link_to_route('player.show', $soldier->player->game->Name, [
                                                $soldier->player->PlayerID,
                                                $soldier->player->SoldierName
                                            ], [
                                                'target' => '_blank',
                                                'class' => $soldier->player->game->class_css,
                                                'style' => 'color: white !important',
                                                'tooltip' => $soldier->player->SoldierName
                                            ]) }}
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
