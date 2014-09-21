@section('content')
{{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\AccountController@updateSettings', 'autocomplete' => 'off')) }}
    <div class="row">
        <div class="col-md-12">

            @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{{ $error }}}</div>
            @endforeach

            @if(Session::get('notice'))
                <div class="alert alert-info">{{{ Session::get('notice') }}}</div>
            @endif

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_basic" data-toggle="tab">Basic Info</a></li>
                    <li><a href="#tab_account" data-toggle="tab">Account</a></li>
                    <li><a href="#tab_site" data-toggle="tab">Site</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_basic">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::label('bf3_player_id', 'Battlefield 3 Player ID') }}
                                    {{ Form::text('bf3_player_id', $user->preferences->bf3_playerid, ['class' => 'form-control', 'placeholder' => 'BF3 Player ID']) }}
                                    <p class="help-block">You can find your player id under your player profile</p>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('bf4_player_id', 'Battlefield 4 Player ID') }}
                                    {{ Form::text('bf4_player_id', $user->preferences->bf4_playerid, ['class' => 'form-control', 'placeholder' => 'BF4 Player ID']) }}
                                    <p class="help-block">You can find your player id under your player profile</p>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('timezone', 'Timezone') }}
                                    {{ Form::select('timezone', Helper::generateTimezoneList(), $user->preferences->timezone, array('class' => 'form-control selectpicker', 'data-live-search' => 'true')) }}
                                    <p class="help-block">The time (including your current adjustment) is: <strong>{{ Helper::UTCToLocal(time())->format('jS \\of F Y h:i A') }}</strong></p>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('gravatar', 'Set Avatar') }}
                                    {{ Form::email('gravatar', $user->preferences->gravatar, ['class' => 'form-control', 'placeholder' => 'Gravatar email']) }}
                                    <p class="help-block">Avatars are provided by Gravatar. To sign up for a free avatar, please <a href="http://www.gravatar.com/" target="_blank">visit Gravatar</a> now.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_account">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('email', 'Email Address') }}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        {{ Form::email('email', NULL, ['class' => 'form-control', 'placeholder' => $user->email, 'autocomplete' => 'off']) }}
                                    </div>
                                    <p class="help-block">Leave blank to keep your current email</p>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('lang', 'Language') }}
                                    {{ Form::select('lang', array('en' => 'English', 'de' => 'German'), $user->preferences->lang, array('class' => 'form-control')) }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('password', 'Password') }}
                                    {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                    <div id="password_crack_result" class="label label-danger"></div>
                                    <p class="help-block">Leave blank to keep your current password</p>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('password_confirmation', 'Confirm Password') }}
                                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_site">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('report_enable', 'Enable Reports') }}
                                    {{ Form::checkbox('report_enable', 1, $user->preferences->report_notify_alert, ['class' => 'minimal']) }}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('report_enable_sound', 'Enable Report Alert Sound') }}
                                    {{ Form::checkbox('report_enable_sound', 1, $user->preferences->report_notify_sound, ['class' => 'minimal']) }}
                                    <p class="help-block">If Enable Reports is not checked then sounds will not play.</p>
                                </div>

                                <div class="form-group">
                                    <label for="report_sound_file">Choose alert sound</label>
                                    {{ Form::select('report_sound_file', $sound_files, $user->preferences->report_notify_sound_file, ['class' => 'form-control']) }}
                                    <p class="help-block">Select a sound file to be played when a player report comes in.</p>
                                </div>

                                <div class="form-group">
                                    <button id="report_sound_controls_play" class="btn btn-sm btn-primary">Play</button>
                                    <button id="report_sound_controls_stop" class="btn btn-sm btn-primary">Stop</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{ Form::submit('Save Changes', ['class' => 'btn bg-olive']) }}
        </div>
    </div>
{{ Form::close() }}
@stop

@section('javascript')
<script type="text/javascript">
$('.selectpicker').selectpicker();

var audioElement = document.createElement('audio');
var file = $("select[name='report_sound_file'] option").filter(":selected").val();

$("select[name='report_sound_file']").change(function() {
    file = $("select[name='report_sound_file'] option").filter(":selected").val();
    audioElement.setAttribute('src', '/audio/' + file + '/' + file + '.mp3');
});

$("#report_sound_controls_play").click(function(event) {
    event.preventDefault();
    audioElement.setAttribute('src', '/audio/' + file + '/' + file + '.mp3');
    audioElement.setAttribute('autoplay', 'autoplay');

    $.get();

    audioElement.addEventListener("load", function() {
        audioElement.play();
    }, true);

    audioElement.play();
    audioElement.loop = true;
});

$("#report_sound_controls_stop").click(function(event) {
    event.preventDefault();
    audioElement.pause();
});

$("#password").keyup(function(){var textValue=$(this).val();var result=zxcvbn(textValue);var div=$("#password_crack_result");div.show().html("Time to crack: "+result.crack_time_display);if(result.score>=0&&result.score<=1){div.addClass("label-danger");div.removeClass("label-primary");div.removeClass("label-success")}else if(result.score>1&&result.score<=3){div.removeClass("label-danger");div.addClass("label-primary");div.removeClass("label-success")}else if(result.score>3){div.removeClass("label-danger");
div.removeClass("label-primary");div.addClass("label-success")}});
</script>
@stop

@section('jsinclude')
<script src="{{ asset('js/zxcvbn.js') }}" type="text/javascript"></script>
@stop
