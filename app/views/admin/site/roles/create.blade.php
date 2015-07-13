@extends('layout.main')

@section('content')
{{ Former::open()->route('admin.site.roles.store') }}
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="box box-primary">
            <div class="box-body">
                {{ Former::text('role_name')->label('Name') }}
                {{ Former::select('permissions[]')->label('Permissions')
                    ->options($permissions)
                    ->multiple()->size(count($permissions, COUNT_RECURSIVE))
                    ->help('Hold CTRL to select multiple permissions.')
                }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ Lang::get('site.admin.roles.create.buttons.save') }}</span>
                        </button>
                        {{ link_to_route('admin.site.roles.index', Lang::get('site.admin.roles.create.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{ Former::close() }}
@stop
