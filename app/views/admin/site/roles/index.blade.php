@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools">
                    {{ link_to_route('admin.site.roles.create', 'Create Role', [], ['class' => 'btn bg-green btn-xs pull-right', 'target' => '_self']) }}
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Role</th>
                            <th>Users Assigned</th>
                            <th>Created</th>
                            <th>Last Updated</th>
                        </thead>

                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ link_to_route('admin.site.roles.edit', $role->name, $role->id, ['target' => '_self']) }}</td>
                                <td>{{ $role->users()->count() }}</td>
                                <td ng-bind="moment('{{ $role->created_at->toIso8601String() }}').format('LLL')"></td>
                                <td ng-bind="moment('{{ $role->updated_at->toIso8601String() }}').format('LLL')"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
