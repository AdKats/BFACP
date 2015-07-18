@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                {{ Former::open() }}
                @foreach($settings as $setting)
                    @if($setting->setting_type == 'multiline' && $setting->setting_name != 'Custom HTML Addition' && is_array($setting->setting_value))
                    {{ Former::textarea($setting->setting_name)->value(implode("\n", $setting->setting_value))->label($setting->setting_name)->rows(count($setting->setting_value))->cols(50) }}
                    @elseif($setting->setting_type == 'multiline' && $setting->setting_name != 'Custom HTML Addition' && !is_array($setting->setting_value))
                    {{ Former::text($setting->setting_name)->value($setting->setting_value)->label($setting->setting_name) }}
                    @elseif($setting->setting_type == 'bool')
                    {{ Former::radios($setting->setting_name)->label($setting->setting_name)->radios([
                        'Enabled' => ['name' => $setting->setting_name, 'value' => 'true'],
                        'Disabled' => ['name' => $setting->setting_name, 'value' => 'false']
                    ])->inline()->check([
                        'true' => $setting->setting_value,
                        'false' => !$setting->setting_value
                    ]) }}
                    @elseif($setting->setting_type == 'int')
                    {{ Former::number($setting->setting_name)->value($setting->setting_value)->label($setting->setting_name) }}
                    @elseif($setting->setting_type == 'double')
                    {{ Former::number($setting->setting_name)->value($setting->setting_value)->label($setting->setting_name)->step('any') }}
                    @else
                    {{ Former::textarea($setting->setting_name)->value($setting->setting_value)->label($setting->setting_name)->rows(4)->cols(50) }}
                    @endif
                @endforeach
                {{ Former::close() }}
            </div>
        </div>
    </div>
</div>
@stop
