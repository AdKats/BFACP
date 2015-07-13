@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th width="250px">Server</th>
                            <th width="150px">Time</th>
                            <th>Source</th>
                            <th>Target</th>
                            <th>Reason</th>
                            <th width="190px">Action</th>
                        </thead>

                        <tbody>
                            @foreach($reports as $report)
                            <tr>
                                <td>
                                    <span tooltip="{{ $report->server->ServerName }}">
                                    {{ $report->server->server_name_short or str_limit($report->server->ServerName, 30) }}
                                    </span>
                                </td>
                                <td><span ng-bind="moment('{{ $report->stamp }}').fromNow()" tooltip="<?php echo '{{';?> moment('<?php echo $report->stamp;?>').format('lll') <?php echo '}}';?>"></span></td>
                                <td>
                                    @if(is_null($report->source_id))
                                    {{ $report->source_name }}
                                    @else
                                    {{ link_to_route('player.show', $report->source_name, [$report->source_id, $report->source_name], ['target' => '_self']) }}</td>
                                    @endif
                                <td>
                                    @if(is_null($report->target_id))
                                    {{ $report->target_name }}
                                    @else
                                    {{ link_to_route('player.show', $report->target_name, [$report->target_id, $report->target_name], ['target' => '_self']) }}
                                    @endif
                                </td>
                                <td>{{ $report->record_message }}</td>
                                <td>{{ Former::select('report_action')->options($commands)->placeholder('Select Action')->addClass('input-sm') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-left">Total: <span ng-bind="{{ $reports->getTotal() }} | number"></span></div>
                <div class="pull-right">{{ $reports->appends(Input::except('page'))->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
