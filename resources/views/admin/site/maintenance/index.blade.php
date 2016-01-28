@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    {!! Former::open()->route('admin.site.maintenance.update') !!}

                    <div class="form-inline">
                        <label for="maintenance_mode" class="col-sm-2 control-label">Maintenance Mode</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="maintenance_mode" value="1" @if(env('APP_DOWN')) checked @endif>
                                    Enabled
                                </label>
                                &nbsp;
                                <label>
                                    <input type="radio" name="maintenance_mode" value="0" @if(! env('APP_DOWN')) checked @endif>
                                    Disabled
                                </label>
                            </div>

                            <div class="help-block">
                                Enable or Disable maintenance mode. If enabled the site will be unavailable unless IP Whitelisted.
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cache_flush" class="col-sm-2 control-label">Clear Cache</label>

                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="cache_flush" value="1">
                                    Enabled
                                </label>
                            </div>

                            <div class="help-block">
                                If checked this will clear all cached data from the application.
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>Save</span>
                            </button>
                        </div>
                    </div>

                    {!! Former::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
