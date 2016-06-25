@extends('layout.main')

@section('content')
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{ $user->gravatar }}" alt="User profile picture">

                    <h3 class="profile-username text-center">{{ $user->username }}</h3>

                    <p class="text-muted text-center">{{ $user->roles[0]->name }}</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Followers</b> <a class="pull-right">1,322</a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b> <a class="pull-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>Friends</b> <a class="pull-right">13,287</a>
                        </li>
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        @foreach($records as $record)
                            <!-- Post -->
                            <div class="post">
                                <div class="user-block">
                                    @if(!is_null($record->source_id))
                                    <img class="img-circle img-bordered-sm" src="{{ $record->source->battlelog->gravatar_img or $record->source->rank_image }}" alt="{{ $record->source_name }} Avatar">
                                    <span class="username" style="margin-bottom: 5px">
                                        <span class="{{ $record->source->game->class_css }}">{{ $record->source->game->Name }}</span>
                                        {!! link_to_route('player.show', $record->source_name,[$record->source_id, $record->source_name]) !!}
                                    </span>
                                    @else
                                        <span class="username" style="margin-bottom: 5px">
                                            <a href="javascript://">{{ $record->target_name }}</a>
                                        </span>
                                    @endif
                                    <span class="description">
                                        {{ $record->type->command_name }} &mdash;
                                        <span ng-bind="moment('{{ $record->stamp }}').fromNow()" tooltip="<?php echo '{{';?> moment('<?php echo $record->stamp;?>').format('lll') <?php echo '}}';?>"></span>
                                    </span>
                                </div>
                                <!-- /.user-block -->
                                <p>
                                    Issued <strong><i>{{ $record->type->command_name }}</i></strong> on {{ $record->target_name }} in server <strong><i>{{ $record->server->ServerName }}</i></strong>.
                                    <pre>{{ $record->record_message }}</pre>
                                </p>
                            </div>
                            <!-- /.post -->
                        @endforeach
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@stop
