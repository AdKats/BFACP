@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-body">
                    <p class="pull-right">Total: <span class="label label-primary">{{ Helper::bytesToSize($dbtotal[0]->size_of_table) }}</span></p>
                    <div class="table-responsive">
                        <table class="table table-condensed table-stripe" id="table_size_data">
                            <thead>
                                <th>Table</th>
                                <th>Rows</th>
                                <th>Size</th>
                            </thead>

                            <tbody>
                                @foreach($dbtables as $table)
                                <tr>
                                    <td>{{ $table->tables }}</td>
                                    <td>{{ number_format($table->rowlength) }}</td>
                                    <td>{{ Helper::bytesToSize($table->size_of_table) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6" id="chart"></div>
    </div>
@stop
@section('javascript')
<script src="//code.highcharts.com/highcharts-3d.js"></script>
<script type="text/javascript">
$(function () {
    $('#chart').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                innerSize: 100,
                depth: 45,
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Table Sizes',
            data: <?php echo $piedata; ?>
        }]
    });
});
</script>
@stop
