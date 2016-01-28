@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    {!! Former::open()->id('ban-form')
                        ->route('admin.adkats.bans.store')
                        ->rules([
                            'message' => 'required'
                        ]) !!}

                    {!! Form::hidden('player_id', $player->PlayerID) !!}

                    {!! Former::text('player')->value($player->SoldierName)->label(trans('adkats.bans.edit.fields.field1'))->disabled() !!}

                    {!! Former::text('admin')->value($admin->SoldierName)->label(trans('adkats.bans.edit.fields.field2'))->disabled() !!}

                    {!! Former::text('notes')->value('NoNotes')->label(trans('adkats.bans.edit.fields.field3'))->maxlength(150) !!}

                    {!! Former::text('message')->label(trans('adkats.bans.edit.fields.field4'))->maxlength(500) !!}

                    {!! Former::select('server')->options($servers)->label(trans('adkats.bans.edit.fields.field5')) !!}

                    <div class="form-group" id="ban-range-container">
                        <label class="control-label col-lg-2 col-sm-4">{!! trans('adkats.bans.edit.fields.field6') !!}</label>

                        <div class="col-lg-10 col-sm-8">
                            <div id="ban-range">
                                <i class="fa fa-calendar fa-lg"></i>&nbsp;
                                <span></span> <strong class="caret"></strong>
                            </div>
                            {!! Form::hidden('banStartDateTime') !!}
                            {!! Form::hidden('banEndDateTime') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-inline">
                            <label for="type" class="col-sm-2 control-label">{{ trans('adkats.bans.edit.fields.field8') }}</label>

                            <div class="col-sm-10">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type" value="8">
                                        {{ trans('player.profile.bans.type.permanent.long') }}
                                    </label>
                                    &nbsp;
                                    <label>
                                        <input type="radio" name="type" value="7" checked>
                                        {{ trans('player.profile.bans.type.temporary.long') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="enforce_guid" class="col-sm-2 control-label">{{ trans('adkats.bans.edit.fields.field9') }}</label>

                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="enforce_guid" value="1" checked>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="enforce_name" class="col-sm-2 control-label">{{ trans('adkats.bans.edit.fields.field10') }}</label>

                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="enforce_name" value="1">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="enforce_ip" class="col-sm-2 control-label">{{ trans('adkats.bans.edit.fields.field11') }}</label>

                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="enforce_ip" value="1">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-sm-offset-4 col-lg-10 col-sm-8">
                            <button type="submit" class="btn bg-green">
                                <i class="fa fa-floppy-o"></i>&nbsp;<span>Create Ban</span>
                            </button>
                            {!! link_to_route('admin.adkats.bans.index', trans('adkats.bans.edit.buttons.cancel'), [], ['class' => 'btn bg-red', 'target' => '_self']) !!}
                        </div>
                    </div>
                    {!! Former::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('js/plugins/daterangepicker/daterangepicker.js') !!}
    <script type="text/javascript">
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            $("input[name='type']").on('ifChecked', function (event) {
                var type = $("input[name='type']:checked").val();

                if (type == 8) {
                    $('#ban-range-container').hide('slow');
                } else {
                    $('#ban-range-container').show('slow');
                }
            });

            if ($("input[name='type']:checked").val() == 8) {
                $('#ban-range-container').hide();
            }

            function updateBanRangeDisplay(date1, date2) {
                $('#ban-range span').html(moment(date1).format('LLL') + '&nbsp;&ndash;&nbsp;' + moment(date2).format('LLL'));
            }

            updateBanRangeDisplay(moment(), moment().add(1, 'h'));
            $("input[name='banStartDateTime']").val(moment().format());
            $("input[name='banEndDateTime']").val(moment().add(1, 'h').format());

            $('#ban-range').daterangepicker({
                ranges: {
                    '1 Hour': [moment(), moment().add(1, 'h')],
                    '2 Hours': [moment(), moment().add(2, 'h')],
                    '3 Hours': [moment(), moment().add(3, 'h')],
                    '1 Day': [moment(), moment().add(1, 'd')],
                    '2 Days': [moment(), moment().add(2, 'd')],
                    '3 Days': [moment(), moment().add(3, 'd')],
                    '1 Week': [moment(), moment().add(1, 'w')],
                    '2 Weeks': [moment(), moment().add(2, 'w')],
                    '3 Weeks': [moment(), moment().add(3, 'w')],
                    '1 Month': [moment(), moment().add(1, 'M')],
                    '2 Months': [moment(), moment().add(2, 'M')],
                    '3 Months': [moment(), moment().add(3, 'M')]
                },
                startDate: moment(),
                endDate: moment(),
                minDate: moment().subtract(1, 'd'),
                timePicker: true,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                timePickerSeconds: false
            }, function (startDate, endDate) {
                updateBanRangeDisplay(startDate, endDate);
                $("input[name='banStartDateTime']").val(moment(startDate).format());
                $("input[name='banEndDateTime']").val(moment(endDate).format());
            });

            $('#ban-form').submit(function (e) {
                var btn = $(this).find('button');
                btn.find("i").removeClass('fa-eraser').addClass('fa-spinner fa-pulse');
                btn.attr('disabled', true);
                btn.find('span').text("<?php echo trans('adkats.bans.edit.buttons.submit.text2');?>");
            });
        });
    </script>
@stop
