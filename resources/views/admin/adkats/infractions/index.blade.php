@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">&nbsp;</h3>
                    <div class="box-tools">
                        <div class="pull-right">
                            {!! Former::text('player')->placeholder(trans('common.nav.extras.psearch.placeholder')) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <th>{{ trans('tables.playerlist.columns.col4') }}</th>
                                <th>{{ trans('player.profile.details.items.reputation') }}</th>
                                <th>{{ trans('player.profile.infractions.table.col2') }}</th>
                                <th>{{ trans('player.profile.infractions.table.col3') }}</th>
                                <th>{{ trans('player.profile.infractions.table.col4') }}</th>
                            </thead>

                            <tbody>
                            @foreach($infractions as $infraction)
                                <tr>
                                    <td>
                                        <span class="{{ $infraction->player->game->class_css }}">{{ $infraction->player->game->Name }}</span>
                                        {!! link_to_route('player.show', $infraction->player->SoldierName, [$infraction->player->PlayerID, $infraction->player->SoldierName], ['target' => '_self']) !!}
                                    </td>
                                    <td>
                                        <span class="{{ $infraction->player->reputation->color }}">
                                            @if($infraction->player->reputation->total_rep_co > 0)
                                                <i class="fa fa-caret-up"></i>
                                            @elseif($infraction->player->reputation->total_rep_co < 0)
                                                <i class="fa fa-caret-down"></i>
                                            @elseif($infraction->player->reputation->total_rep_co == 0)
                                                <i class="fa fa-caret-left"></i>
                                            @endif

                                            {{ round($infraction->player->reputation->total_rep_co, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ $infraction->punish_points }}</td>
                                    <td>{{ $infraction->forgive_points }}</td>
                                    <td>{{ $infraction->total_points }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    <div class="pull-left">Total: <span ng-bind="{{ $infractions->total() }} | number"></span></div>
                    <div class="pull-right">{!! $infractions->appends(\Illuminate\Support\Facades\Input::except('page'))->links() !!}</div>
                </div>
            </div>
        </div>
    </div>
@stop
