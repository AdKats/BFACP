@extends('layout.main')

@section('content')
{{ Former::open()->route('admin.adkats.users.update', [$user->user_id]) }}
<div class="row">
    <div class="col-xs-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ Lang::get('adkats.users.edit.details') }}</h3>
            </div>

            <div class="box-body">
                {{ Former::text('user_name')->label(Lang::get('adkats.users.edit.inputs.username.label')) }}
                {{ Former::email('user_email')->label(Lang::get('adkats.users.edit.inputs.email.label')) }}
                {{ Former::select('user_role')->options($roles)->label(Lang::get('adkats.users.edit.inputs.role.label')) }}
                {{ Former::date('user_expiration')
                    ->forceValue($user->user_expiration->toDateString())
                    ->min(Carbon::now()->toDateString())
                    ->max(Carbon::now()->addYears(30)->toDateString())
                    ->label(Lang::get('adkats.users.edit.inputs.expiration.label'))
                    ->help(Lang::get('adkats.users.edit.inputs.expiration.help')) }}
                {{ Former::text('user_notes')->maxlength(1000)->label(Lang::get('adkats.users.edit.inputs.notes.label')) }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ Lang::get('adkats.users.edit.buttons.save') }}</span>
                        </button>
                        {{ link_to_route('admin.adkats.users.index', Lang::get('adkats.users.edit.buttons.cancel'), [], ['class' => 'btn bg-red']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-6">
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
                    {{ HTML::faicon('fa-info-circle') }} {{ Lang::get('adkats.users.no_soldiers') }}
                </alert>
                @endif
            </div>
        </div>
    </div>
</div>
{{ Former::close() }}
@stop
