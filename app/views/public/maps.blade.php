@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <div class="box-body">
                <div id="maps_container"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
$(function () {
    $.getJSON('/api/v1/common/general/player-maps', function (data) {

        // Add lower case codes to the data set for inclusion in the tooltip.pointFormat
        $.each(data.data, function () {
            this.flag = this.code.replace('UK', 'GB').toLowerCase();
        });

        // Initiate the chart
        $('#maps_container').highcharts('Map', {

            title: {
                text: ''
            },

            legend: {
                title: {
                    text: 'Players',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }
            },

            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                }
            },

            tooltip: {
                backgroundColor: 'none',
                borderWidth: 0,
                shadow: false,
                useHTML: true,
                padding: 0,
                pointFormat: '<span class="f32"><span class="flag {point.flag}"></span></span>'
                    + ' {point.name}: <b>{point.value}</b> Player(s)',
                positioner: function () {
                    return { x: 0, y: 450 };
                }
            },

            colorAxis: {
                min: 1,
                type: 'logarithmic'
            },

            series : [{
                data : data.data,
                mapData: Highcharts.maps['custom/world'],
                joinBy: ['iso-a2', 'code'],
                name: 'Player density',
                states: {
                    hover: {
                        color: '#BADA55'
                    }
                }
            }]
        });
    });
});
</script>
@stop

@section('stylesinclude')
<style type="text/css">
#maps_container {
    height: 700px;
}

.highcharts-tooltip>span {
    padding: 10px;
    white-space: normal !important;
    width: 250px;
}

.loading {
    margin-top: 10em;
    text-align: center;
    color: gray;
}

.f32 .flag {
    vertical-align: middle !important;
}
</style>
<link rel="stylesheet" type="text/css" href="//cloud.github.com/downloads/lafeber/world-flags-sprite/flags32.css" />
@stop

@section('jsinclude')
<script src="//code.highcharts.com/maps/highmaps.js"></script>
<script src="//code.highcharts.com/maps/modules/data.js"></script>
<script src="//code.highcharts.com/maps/modules/exporting.js"></script>
<script src="//code.highcharts.com/mapdata/custom/world.js"></script>
@stop
