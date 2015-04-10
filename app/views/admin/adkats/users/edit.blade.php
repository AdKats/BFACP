@extends('layout.main')

@section('content')
{{ Former::open()->route('admin.adkats.users.update', [$user->user_id]) }}
<div class="row">
    <div class="col-xs-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Details</h3>
            </div>

            <div class="box-body">
                {{ Former::text('user_name')->label('Username') }}
                {{ Former::email('user_email')->label('Email') }}
                {{ Former::select('user_role')->options($roles)->label('Role') }}
                {{ Former::date('user_expiration')
                    ->forceValue($user->user_expiration->toDateString())
                    ->min(Carbon::now()->toDateString())
                    ->max(Carbon::now()->addYears(30)->toDateString())
                    ->label('Expiration')
                    ->help('Leave date blank to set default expire date.') }}
                {{ Former::text('user_notes')->maxlength(1000)->label('Notes') }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>Save Changes</span>
                        </button>
                        {{ link_to_route('admin.adkats.users.index', 'Cancel', [], ['class' => 'btn bg-red']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Soldiers</h3>
            </div>

            <div class="box-body">
                {{ Former::text('soldiers')
                    ->forceValue(implode(',', $user->soldiers()->lists('player_id')))
                    ->label('Player IDs')
                    ->help('Seprate IDs by a comma to add more players. Remove IDs to delete them from the user.') }}

                {{ Former::text('soldier')->label('Player Name')->help('To have the system add the player, type in the player name. This will add any player with the name provided.') }}

                @if($user->soldiers->count() > 0)
                <table class="table table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Game</th>
                        <th>Name</th>
                    </thead>
                    <tbody>
                        @foreach($user->soldiers as $soldier)
                        <tr>
                            <td>{{ $soldier->player->PlayerID }}</td>
                            <td><label class="{{ $soldier->player->game->class_css }}">{{ $soldier->player->game->Name }}</label></td>
                            <td>{{ link_to_route('player.show', $soldier->player->SoldierName, [
                                $soldier->player->PlayerID,
                                $soldier->player->SoldierName
                            ], ['target' => '_blank']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <alert type="info">
                    {{ HTML::faicon('fa-info-circle') }} No soldiers found.
                </alert>
                @endif
            </div>
        </div>
    </div>
</div>
{{ Former::close() }}
@stop
