@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">&nbsp;</h3>

                    <div class="box-tools">
                        {!! link_to_route('admin.site.users.create', trans('navigation.admin.site.items.users.items.create.title'), [], ['class' => 'btn bg-green btn-xs pull-right', 'target' => '_self']) !!}
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed">
                            <thead>
                            <th>{{ trans('site.admin.users.listing.table.col1') }}</th>
                            <th>{{ trans('site.admin.users.listing.table.col2') }}</th>
                            <th>{{ trans('site.admin.users.listing.table.col3') }}</th>
                            <th>{{ trans('site.admin.users.listing.table.col4') }}</th>
                            <th>{{ trans('site.admin.users.listing.table.col6') }}</th>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{!! link_to_route('admin.site.users.edit', $user->username, $user->id, ['target' => '_self']) !!}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if(isset($user->roles[0]))
                                            {{ $user->roles[0]->name }}
                                        @else
                                            <span class="text-red">No Role Linked - Sys. Error</span>
                                        @endif
                                    </td>
                                    <td>{{ MainHelper::languages($user->setting->lang) }}</td>
                                    <td ng-bind="moment('{{ $user->stamp }}').format('LLL')"></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    <div class="pull-left">Total: <span ng-bind="{{ $users->total() }} | number"></span></div>
                    <div class="pull-right">{!! $users->links() !!}</div>
                </div>
            </div>
        </div>
    </div>
@stop
