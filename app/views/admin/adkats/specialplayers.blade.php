@section('content')

{{ Form::hidden('locale', Auth::user()->preferences->lang) }}

@include('angular.admin.AdKats.specialplayers')

@stop

@section('jsinclude')
<script src="{{ asset('js/BFAdminCP/controllers/AdKatsSpecialPlayersCtrl.js') }}"></script>
@stop
