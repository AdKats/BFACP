@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12">
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="javascript://">Severity Levels</a>
                </div>

                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        {{ HTML::nav_item(sprintf('%s/%s/%s/%s/%s', $url, $path, $sapi_plain, $date, 'all'), ucfirst(Lang::get('logviewer::logviewer.levels.all'))) }}
                        @foreach($levels as $level)
                            {{ HTML::nav_item(sprintf('%s/%s/%s/%s/%s', $url, $path, $sapi_plain, $date, $level), ucfirst(Lang::get('logviewer::logviewer.levels.'.$level))) }}
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-4 col-md-3">
        @if(!$empty)
        <div class="well well-sm">
            <ul class="nav">
                @forelse($logs as $type => $files)
                    @forelse($files['logs'] as $app => $file)
                        @unless(empty($file))
                        <li class="nav-header">
                            @if(count($files['logs']) > 1)
                            {{ $app }} &ndash;
                            @endif
                            {{ $files['sapi'] }}
                        </li>
                        <ul class="nav nav-pills nav-stacked">
                            @foreach($file as $f)
                                {{ HTML::decode(HTML::nav_item(sprintf('%s/%s/%s/%s', $url, $app, $type, $f), $f)) }}
                            @endforeach
                        </ul>
                        @endunless
                    @empty
                    <p class="alert alert-info">{{ Lang::get('logviewer::logviewer.empty_file', ['sapi' => $sapi, 'date' => $date]) }}</p>
                    @endforelse
                @empty
                <p class="alert alert-info">{{ Lang::get('logviewer::logviewer.no_log', ['sapi' => $sapi, 'date' => $date]) }}</p>
                @endforelse
            </ul>
        </div>
        @else
        <p class="alert alert-info">{{ Lang::get('logviewer::logviewer.no_log', ['sapi' => $sapi, 'date' => $date]) }}</p>
        @endif
    </div>

    <div class="col-xs-12 col-sm-8 col-md-9">
        @unless($empty)
            {{ $paginator->links() }}
            @forelse($log as $l)
            <div class="box box-solid box-{{ MainHelper::alertToBoxClass($l['level']) }} collapsed-box">
                <div class="box-header with-border">
                    <div class="box-title">{{ $l['header'] }}</div>
                    @if(strlen($l['stack']) > 1)
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                    @endif
                </div>

                @if(strlen($l['stack']) > 1)
                <div class="box-body"><pre>{{ $l['stack'] }}</pre></div>
                @endif
            </div>
            @empty
            <p class="alert alert-info">{{ Lang::get('logviewer::logviewer.empty_file', ['sapi' => $sapi, 'date' => $date]) }}</p>
            @endforelse
            {{ $paginator->links() }}
        @endunless
    </div>
</div>
@stop
