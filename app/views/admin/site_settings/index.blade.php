@section('content')

<div class="row">

    @if(Session::has('messages'))
        @foreach(Session::get('messages') as $message)
        <div class="col-md-12">
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Success!</b> {{ $message }}
            </div>
        </div>
        @endforeach
    @endif

    <div class="col-md-12">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        @endforeach

        <div class="box box-primary">
            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\Admin\\SiteController@store', 'class' => 'form-horizontal')) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('BF3', 'BF3', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('BF3', 1, ($settings['BF3']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('BF3', 0, (!$settings['BF3']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['BF3']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('BF4', 'BF4', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('BF4', 1, ($settings['BF4']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('BF4', 0, (!$settings['BF4']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['BF4']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ONLYAUTHUSERS', 'Require Login', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ONLYAUTHUSERS', 1, ($settings['ONLYAUTHUSERS']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('ONLYAUTHUSERS', 0, (!$settings['ONLYAUTHUSERS']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['ONLYAUTHUSERS']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('FORCESSL', 'Force SSL', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('FORCESSL', 1, ($settings['FORCESSL']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('FORCESSL', 0, (!$settings['FORCESSL']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['FORCESSL']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('UPTIMEROBOT', 'Uptime Robot', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('UPTIMEROBOT', 1, ($settings['UPTIMEROBOT']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('UPTIMEROBOT', 0, (!$settings['UPTIMEROBOT']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['UPTIMEROBOT']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('UPTIMEROBOT-KEY', 'Uptime Robot Key', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('UPTIMEROBOT-KEY', $settings['UPTIMEROBOT-KEY']['value'], ['class' => 'form-control'])}}
                            <p class="help-block">{{ $settings['UPTIMEROBOT-KEY']['description'] }}. You can find your API key under "My Settings" at <a href="https://uptimerobot.com/dashboard.php#mySettings" target="_blank">uptimerobot.com</a></p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('MB-KEY', 'Metabans API Key', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('MB-KEY', $settings['MB-KEY']['value'], ['class' => 'form-control'])}}
                            <p class="help-block">{{ $settings['MB-KEY']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('MB-USR', 'Metabans API Username', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('MB-USR', $settings['MB-USR']['value'], ['class' => 'form-control'])}}
                            <p class="help-block">{{ $settings['MB-USR']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('MB-ACC', 'Metabans Account', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('MB-ACC', $settings['MB-ACC']['value'], ['class' => 'form-control'])}}
                            <p class="help-block">{{ $settings['MB-ACC']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('SERVERORDER', 'Server Sort Method', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::select('SERVERORDER', $serverSort, $settings['SERVERORDER']['value'], ['class' => 'form-control']) }}
                            <p class="help-block">{{ $settings['SERVERORDER']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('MOTD', 'MOTD', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('MOTD', 1, ($settings['MOTD']['value'] ? TRUE: FALSE)) }} Enabled
                            {{ Form::radio('MOTD', 0, (!$settings['MOTD']['value'] ? TRUE: FALSE)) }} Disabled
                            <p class="help-block">{{ $settings['MOTD']['description'] }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('MOTD-TXT', 'MOTD Text', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11 pad">
                            <textarea class="textarea" id="MOTDTXT" name="MOTD-TXT" rows="10" cols="80">
                                {{ htmlspecialchars($settings['MOTD-TXT']['value'], ENT_QUOTES) }}
                            </textarea>
                            <p class="help-block">{{ $settings['MOTD-TXT']['description'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Update Settings', array('class' => 'btn btn-primary')) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex', 'Cancel', [], ['class' => 'btn bg-olive']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
CKEDITOR.replace('MOTD-TXT');
</script>
@stop

@section('jsinclude')
<script src="{{ asset('js/plugins/ckeditor/ckeditor.js') }}"></script>
@stop
