@extends('layout.main')

@section('content')
    {!! Former::open()->route('user.account.save')->rules([
        'email' => 'required|email',
        'language' => 'required'
    ]) !!}

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{{ trans('user.account.settings.blocks.general.title') }}</h3>
                </div>

                <div class="box-body">
                    {!! Former::email('email')->label(trans('user.account.settings.blocks.general.inputs.email')) !!}
                    {!! Former::select('language')->label(trans('user.account.settings.blocks.general.inputs.language'))->options(Config::get('bfacp.site.languages'))->value($user->setting->lang) !!}
                    {!! Former::checkbox('twofactor_auth')->label("&nbsp;")->text('Enable Two Factor Authentication')->check($user->twofactor_auth) !!}

                    @if(!empty($user->google2fa_secret))
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <img src="{{ Google2FA::getQRCodeGoogleUrl(config('bfacp.site.title'), $user->email, $user->google2fa_secret) }}">
                            </div>
                        </div>
                    @endif


                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>{{ trans('site.admin.users.edit.buttons.save') }}</span>
                            </button>
                            {!! link_to_route('user.account', trans('site.admin.users.edit.buttons.cancel'), [], ['class' => 'btn bg-blue', 'target' => '_self']) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">{{ trans('user.account.settings.blocks.password.title') }}</h3>
                </div>

                <div class="box-body">
                    {!! Former::password('password')->label(trans('user.account.settings.blocks.password.inputs.password'))->help(trans('user.account.settings.blocks.password.inputs_help.password'))->autocomplete('off') !!}
                    {!! Former::password('password_confirmation')->label(trans('user.account.settings.blocks.password.inputs.password_confirm'))->help(trans('user.account.settings.blocks.password.inputs_help.password_confirm'))->autocomplete('off') !!}
                </div>
            </div>
        </div>
    </div>
    {!! Former::close() !!}
@stop

@section('scripts')
    {!! Html::script('js/plugins/zxcvbn/zxcvbn.js') !!}
    <script type="text/javascript">
        $(function () {
            var password = $('input[name="password"]');
            var password_confirmation = $('input[name="password_confirmation"]');

            password.on('keyup keypress blue change', function (event) {
                var val = $(this).val();
                var parent = $(this).parent();
                var crackResult = zxcvbn(val);
                var crackTime = crackResult.crack_time_display;
                var crackScore = crackResult.score;

                if (parent.find('label').length === 0) {
                    parent.prepend('<label class="control-label"><i class="fa"></li>&nbsp;<span></span></label>');
                }

                var label = parent.find('label');

                if (crackScore == 0) {
                    parent.removeClass('has-warning').removeClass('has-success').addClass('has-error');
                    if (!label.find('i').hasClass('fa-times-circle-o')) {
                        label.find('i').removeClass('fa-check-circle-o').removeClass('fa-exclamation-circle').addClass('fa-times-circle-o');
                    }
                } else if (crackScore >= 1 && crackScore <= 3) {
                    parent.removeClass('has-error').removeClass('has-success').addClass('has-warning');
                    if (!label.find('i').hasClass('fa-exclamation-circle')) {
                        label.find('i').removeClass('fa-check-circle-o').removeClass('fa-times-circle-o').addClass('fa-exclamation-circle');
                    }
                } else if (crackScore > 3) {
                    parent.removeClass('has-error').removeClass('has-warning').addClass('has-success');
                    if (!label.find('i').hasClass('fa-check-circle-o')) {
                        label.find('i').removeClass('fa-exclamation-circle').removeClass('fa-times-circle-o').addClass('fa-check-circle-o');
                    }
                }

                if (crackScore == 0) {
                    label.find('span').text('Cracked: ' + crackTime);
                } else {
                    label.find('span').text('Cracked in: ' + crackTime);
                }
            });
        });
    </script>
@stop
