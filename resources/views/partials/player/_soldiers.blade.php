<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">{{ trans('adkats.users.soldiers') }}</h3>
    </div>

    <div class="box-body">
        {!! Former::text('soldiers')
            ->forceValue(implode(',', head($user->soldiers()->lists('player_id'))))
            ->label(trans('adkats.users.edit.inputs.soldiers.label'))
            ->help(trans('adkats.users.edit.inputs.soldiers.help')) !!}

        {!! Former::text('soldier')->label(trans('adkats.users.edit.inputs.soldier.label'))->help(trans('adkats.users.edit.inputs.soldier.help')) !!}

        @if($user->soldiers->count() > 0)
            <table class="table table-condensed table-striped">
                <thead>
                <th>{{ trans('adkats.users.edit.table.col1') }}</th>
                <th>{{ trans('adkats.users.edit.table.col2') }}</th>
                <th>{{ trans('adkats.users.edit.table.col3') }}</th>
                </thead>
                <tbody>
                @foreach($user->soldiers as $soldier)
                    <tr>
                        <td>{{ $soldier->player->PlayerID }}</td>
                        <td>{!! Form::label(null, $soldier->player->game->Name, ['class' => $soldier->player->game->class_css]) !!}</td>
                        <td>{!! link_to_route('player.show', $soldier->player->SoldierName, [
                        $soldier->player->PlayerID,
                        $soldier->player->SoldierName
                    ], ['target' => '_blank']) !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <alert type="info">{!! Macros::faicon('fa-info-circle') !!} {{ trans('adkats.users.no_soldiers') }}</alert>
        @endif
    </div>
</div>
