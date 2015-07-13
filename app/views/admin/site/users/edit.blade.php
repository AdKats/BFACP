@extends('layout.main')

@section('content')
{{ Former::open()->route('admin.site.users.update', [$user->id])->rules([
    'username' => 'required|alpha_num|min:4',
    'email'    => 'required|email',
    'role'     => 'required'
]) }}

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ Lang::get('site.admin.users.edit.details') }}</h3>
            </div>

            <div class="box-body">
                {{ Former::text('username')->label(Lang::get('site.admin.users.edit.inputs.username.label')) }}
                {{ Former::email('email')->label(Lang::get('site.admin.users.edit.inputs.email.label')) }}
                {{ Former::select('role')->options($roles, $user->roles[0]->id)->label(Lang::get('site.admin.users.edit.inputs.role.label')) }}
                {{ Former::inline_radios('account_status')->label(Lang::get('site.admin.users.edit.inputs.account_status.label'))->radios([
                    'Inactive' => 'confirmed',
                    'Active'   => 'confirmed'
                ])->check([
                    'confirmed0' => !$user->confirmed,
                    'confirmed1' => $user->confirmed
                ]) }}
                {{ Former::select('language')->label(Lang::get('site.admin.users.edit.inputs.lang.label'))->options(Config::get('bfacp.site.languages'))->value($user->setting->lang) }}
                {{ Former::checkbox('generate_pass')->text(Lang::get('site.admin.users.edit.inputs.genpass.label'))->label('&nbsp;') }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ Lang::get('site.admin.users.edit.buttons.save') }}</span>
                        </button>
                        {{ link_to_route('admin.site.users.index', Lang::get('site.admin.users.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) }}
                        <button class="btn bg-red" id="delete-user">
                            <i class="fa fa-trash"></i>&nbsp;<span>{{ Lang::get('site.admin.users.edit.buttons.delete') }}</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-6">
        @include('partials.player._soldiers', ['user' => $user])
    </div>
</div>
{{ Former::close() }}
@stop

@section('scripts')
<script type="text/javascript">
    $('#delete-user').click(function(e) {
        e.preventDefault();

        var btn = $(this);

        if(confirm('Are you sure you want to delete {{ $user->username }}? This can\'t be undone.')) {
            btn.find('i').removeClass('fa-trash').addClass('fa-spinner fa-pulse');
            btn.parent().find('button').attr('disabled', true);
            $.ajax({
                url: "{{ route('admin.site.users.destroy', $user->id) }}",
                type: 'DELETE',
            })
            .done(function(data) {
                window.location.href = data.data.url;
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                btn.find('i').removeClass('fa-spinner fa-pulse').addClass('fa-trash');
                btn.parent().find('button').attr('disabled', false);
            });
        }
    });
</script>
@stop
