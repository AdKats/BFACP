<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{ Lang::get('adkats.users.soldiers') }}</h3>
    </div>

    <div class="box-body">
        {{ Former::text('soldiers')
            ->forceValue(implode(',', $user->soldiers()->lists('player_id')))
            ->label(Lang::get('adkats.users.edit.inputs.soldiers.label'))
            ->help(Lang::get('adkats.users.edit.inputs.soldiers.help')) }}

        {{ Former::text('soldier')->label(Lang::get('adkats.users.edit.inputs.soldier.label'))->help(Lang::get('adkats.users.edit.inputs.soldier.help')) }}

        @if($user->soldiers->count() > 0)
        <table class="table table-condensed table-striped">
            <thead>
                <th>{{ Lang::get('adkats.users.edit.table.col1') }}</th>
                <th>{{ Lang::get('adkats.users.edit.table.col2') }}</th>
                <th>{{ Lang::get('adkats.users.edit.table.col3') }}</th>
            </thead>
            <tbody>
                @foreach($user->soldiers as $soldier)
                <tr>
                    <td>{{ $soldier->player->PlayerID }}</td>
                    <td>{{ Form::label(null, $soldier->player->game->Name, ['class' => $soldier->player->game->class_css]) }}</td>
                    <td>{{ link_to_route('player.show', $soldier->player->SoldierName, [
                        $soldier->player->PlayerID,
                        $soldier->player->SoldierName
                    ], ['target' => '_blank']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <alert type="info">{{ HTML::faicon('fa-info-circle') }} {{ Lang::get('adkats.users.no_soldiers') }}</alert>
        @endif
    </div>
</div>
