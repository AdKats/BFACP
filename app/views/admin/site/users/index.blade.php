@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools">
                    {{ link_to_route('admin.site.users.create', Lang::get('navigation.admin.site.items.users.items.create.title'), [], ['class' => 'btn bg-green btn-xs pull-right', 'target' => '_self']) }}
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>{{ Lang::get('site.admin.users.listing.table.col1') }}</th>
                            <th>{{ Lang::get('site.admin.users.listing.table.col2') }}</th>
                            <th>{{ Lang::get('site.admin.users.listing.table.col3') }}</th>
                            <th>{{ Lang::get('site.admin.users.listing.table.col4') }}</th>
                            <th>{{ Lang::get('site.admin.users.listing.table.col5') }}</th>
                            <th>{{ Lang::get('site.admin.users.listing.table.col6') }}</th>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ link_to_route('admin.site.users.edit', $user->username, $user->id, ['target' => '_self']) }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles[0]->name }}</td>
                                <td>{{ MainHelper::languages($user->setting->lang) }}</td>
                                <td>
                                    @if($user->confirmed)
                                    <span class="label bg-green">{{ Lang::get('site.admin.users.listing.status.active') }}</span>
                                    @else
                                    <span class="label bg-red">{{ Lang::get('site.admin.users.listing.status.inactive') }}</span>
                                    @endif
                                </td>
                                <td ng-bind="moment('{{ $user->stamp }}').format('LLL')"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left">Total: <span ng-bind="{{ $users->getTotal() }} | number"></span></div>
                <div class="pull-right">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
