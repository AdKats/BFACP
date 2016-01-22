@extends('layout.main')

@section('content')
    {!! Former::open()->route('admin.site.users.store')->rules([
        'username' => 'required|alpha_num|min:4',
        'email'    => 'required|email',
        'role'     => 'required',
        'language' => 'required',
    ]) !!}

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{{ trans('site.admin.users.edit.details') }}</h3>
                </div>

                <div class="box-body">
                    {!! Former::text('username')->label(trans('site.admin.users.edit.inputs.username.label')) !!}
                    {!! Former::text('soldier')->label(trans('adkats.users.edit.inputs.soldier.label'))->help(trans('adkats.users.edit.inputs.soldier.help')) !!}
                    {!! Former::email('email')->label(trans('site.admin.users.edit.inputs.email.label')) !!}
                    {!! Former::select('role')->options($roles, 2)->label(trans('site.admin.users.edit.inputs.role.label')) !!}
                    {!! Former::select('language')->label(trans('site.admin.users.edit.inputs.lang.label'))->options(Config::get('bfacp.site.languages'))->value(Config::get('app.locale')) !!}

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ trans('site.admin.users.create.buttons.save') }}</span>
                            </button>
                            {!! link_to_route('admin.site.users.index', trans('site.admin.users.create.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) !!}
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
    {!! Former::close() !!}
@stop
