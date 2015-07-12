@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools">
                    {{ link_to_route('admin.adkats.roles.create', 'Create Role', [], ['class' => 'btn bg-green btn-xs pull-right', 'target' => '_self']) }}
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th>Name</th>
                            <th>Users</th>
                            <th>Power Level</th>
                        </thead>

                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ link_to_route('admin.adkats.roles.edit', $role->role_name, $role->role_id, ['target' => '_self']) }}</td>
                                <td>{{ count($role->users) }}</td>
                                <td>{{ $role->getPowerLevel($guestCommandCount) }}</td>
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
