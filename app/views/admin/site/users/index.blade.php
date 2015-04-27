@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">&nbsp;</h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Lang</th>
                            <th>Status</th>
                            <th>Created</th>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles[0]->name }}</td>
                                <td>{{ $user->setting->lang }}</td>
                                <td>
                                    @if($user->confirmed)
                                    <span class="label bg-green">Active</span>
                                    @else
                                    <span class="label bg-red">Inactive</span>
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
