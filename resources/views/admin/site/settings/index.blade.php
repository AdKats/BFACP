@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    {!! Former::open()->method('PUT')->route('admin.site.settings.update') !!}
                    @foreach($settings as $setting)
                        @if (!is_null(MainHelper::stringToBool($setting->option_value)))
                            <div class="form-inline">
                                <label for="{{ str_replace('.', '-', $setting->option_key) }}" class="col-sm-2 control-label">{{ $setting->option_title }}</label>

                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="{{ str_replace('.', '-', $setting->option_key) }}" value="1" @if(MainHelper::stringToBool($setting->option_value)) checked @endif>
                                            Enabled
                                        </label>
                                        &nbsp;
                                        <label>
                                            <input type="radio" name="{{ str_replace('.', '-', $setting->option_key) }}" value="0" @if(! MainHelper::stringToBool($setting->option_value)) checked @endif>
                                            Disabled
                                        </label>
                                    </div>

                                    <div class="help-block">
                                        {{ $setting->option_description }}
                                    </div>
                                </div>
                            </div>
                        @elseif($setting->option_key == 'site.motd')
                            {!! Former::textarea(str_replace('.', '-', $setting->option_key))->value($setting->option_value)
                                ->label($setting->option_title)->help($setting->option_description)->rows(5)->cols(50) !!}
                        @else
                            {!! Former::text(str_replace('.', '-', $setting->option_key))->value($setting->option_value)->label($setting->option_title)->help($setting->option_description) !!}
                        @endif
                    @endforeach

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>Save Changes</span>
                            </button>
                        </div>
                    </div>

                    {!! Former::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') !!}
    <script type="text/javascript">
        $(function () {
            $("textarea").wysihtml5({
                "color": true
            });
        });
    </script>
@stop

@section('styles')
    {!! Html::style('css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') !!}
@stop
