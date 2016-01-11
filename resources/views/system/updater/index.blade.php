@extends('layout.main')

@section('content')

<div class="row">
    <div class="col-xs-12">

        <div class="box box-info">
            <div class="box-header with-border">
                <h4 class="box-title">Latest release &ndash; {{ $latest_release['name'] }}</h4>
                <div class="pull-right">
                    @if($outofdate)
                    <small class="label label-danger">Out of Date!</small>
                    @elseif($unreleased)
                    <small class="label label-warning">You're running an unreleased version.</small>
                    @else
                    <small class="label label-success">You're running the latest version</small>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        @if(!empty($latest_release['body']))
                        <p class="lead">{{ $latest_release['body'] }}</p>
                        @endif
                        <p>
                            <a href="{{ $latest_release['zipball_url'] }}" class="btn btn-primary">
                                <i class="fa fa-file-archive-o"></i>
                                Download Zip
                            </a>

                            <a href="{{ $latest_release['tarball_url'] }}" class="btn btn-primary">
                                <i class="fa fa-file-archive-o"></i>
                                Download Gzip
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h2 class="box-title">Release History</h2>
            </div>

            <div class="box-body">
                @foreach($releases as $key => $release)
                <h2>{{ link_to($release['html_url'], $release['name'], ['target' => '_blank']) }} - <small ng-bind="moment('{{ $release['created_at'] }}').fromNow()"></small></h2>

                @if($release['prerelease'])
                <label class="label label-warning">Pre-Release</label>
                @endif

                @if(!empty($release['body']))
                <p class="lead">{{ $release['body'] }}</p>
                @endif

                <p>
                    <a href="{{ $release['zipball_url'] }}" class="btn btn-primary">
                        <i class="fa fa-file-archive-o"></i>
                        Download Zip
                    </a>

                    <a href="{{ $release['tarball_url'] }}" class="btn btn-primary">
                        <i class="fa fa-file-archive-o"></i>
                        Download Gzip
                    </a>
                </p>

                @endforeach
            </div>

        </div>
    </div>
</div>

@stop
