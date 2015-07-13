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
                        {{ link_to_route('admin.adkats.users.index', Lang::get('adkats.users.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) }}
                        <button class="btn bg-red" id="delete-user">
                            <i class="fa fa-trash"></i>&nbsp;<span>{{ Lang::get('adkats.users.edit.buttons.delete') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-6">
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

        if(confirm('Are you sure you want to delete {{ $user->user_name }}? This can\'t be undone.')) {
            btn.find('i').removeClass('fa-trash').addClass('fa-spinner fa-pulse');
            btn.parent().find('button').attr('disabled', true);
            $.ajax({
                url: "{{ route('admin.adkats.users.destroy', $user->user_id) }}",
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
