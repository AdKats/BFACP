@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                {{ Former::open()->method('PUT')->route('admin.site.maintenance.update') }}

                {{ Former::radios('maintenance_mode')->label('Maintenance Mode')->help('Enable or Disable maintenance mode. If enabled the site will be unavailable unless IP Whitelisted.')->radios([
                    'Enabled' => ['name' => 'maintenance_mode', 'value' => '1'],
                    'Disabled' => ['name' => 'maintenance_mode', 'value' => '0']
                ])->inline()->check([
                    '1' => isset($appdown),
                    '0' => !isset($appdown)
                ]) }}

                {{ Former::checkbox('cache_flush')->label('Clear Cache')->help('If checked this will clear all cached data from the application.')->text('&nbsp;') }}

                <div class="form-group">
                    <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                        <button type="submit" class="btn bg-green">
                            <i class="fa fa-floppy-o"></i>&nbsp;<span>Save</span>
                        </button>
                    </div>
                </div>

                {{ Former::close() }}
            </div>
        </div>
    </div>
</div>
@stop
