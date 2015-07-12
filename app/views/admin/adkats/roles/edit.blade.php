@extends('layout.main')

@section('content')
    {{ Former::open()->route('admin.adkats.roles.update', [$role->role_id]) }}
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    {{ Former::text('display_name')->label('Name')->value($role->role_name)->disabled($role->role_id == 1) }}
                    {{ Former::select('permissions[]')->label('Permissions')
                        ->options($permissions)
                        ->select($role->permissions->lists('command_id'))
                        ->multiple()->size(count($permissions, COUNT_RECURSIVE))
                        ->help('Hold CTRL to select multiple permissions.')
                    }}

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ Lang::get('site.admin.roles.edit.buttons.save') }}</span>
                            </button>
                            {{ link_to_route('admin.adkats.roles.index', Lang::get('site.admin.roles.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) }}
                            @if($role->role_id != 1)
                                <button class="btn bg-red" id="delete-role">
                                    <i class="fa fa-trash"></i>&nbsp;<span>{{ Lang::get('site.admin.roles.edit.buttons.delete') }}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Users</h3>
                </div>

                <div class="box-body">
                    <table class="table table-striped table-condensed">
                        <tbody>
                        @foreach($role->users as $user)
                            <tr>
                                <td>{{ link_to_route('admin.adkats.users.edit', $user->user_name, $user->user_id, ['target' => '_blank']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{ Former::close() }}
@stop

@section('scripts')
    <script type="text/javascript">
        $('#delete-role').click(function(e) {
            e.preventDefault();

            var btn = $(this);

            if(confirm('Are you sure you want to delete the role {{ $role->role_name }}? This can\'t be undone.')) {
                btn.find('i').removeClass('fa-trash').addClass('fa-spinner fa-pulse');
                btn.parent().find('button').attr('disabled', true);
                $.ajax({
                    url: "{{ route('admin.adkats.roles.destroy', $role->role_id) }}",
                    type: 'DELETE',
                })
                .done(function(data) {
                    toastr.success('Role Deleted!');
                    window.location.href = data.data.url;
                })
                .fail(function() {
                    console.log("error");
                    toastr.error('Unable to delete role');
                })
                .always(function() {
                    btn.find('i').removeClass('fa-spinner fa-pulse').addClass('fa-trash');
                    btn.parent().find('button').attr('disabled', false);
                });
            }
        });
    </script>
@stop
