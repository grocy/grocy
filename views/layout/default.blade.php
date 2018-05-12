<!DOCTYPE html>
<html lang="{{ CULTURE }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">
	<link rel="icon" type="image/png" sizes="200x200" href="{{ $U('/img/grocy.png?v=') }}{{ $version }}">

	<title>@yield('title') | grocy</title>

	<link href="{{ $U('/bower_components/bootstrap/dist/css/bootstrap.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/font-awesome/css/font-awesome.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/bootstrap-combobox/css/bootstrap-combobox.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/toastr/toastr.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/tagmanager/tagmanager.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/bower_components/bootstrap-side-navbar/source/assets/stylesheets/navbar-fixed-side.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/components_unmanaged/noto-sans-v6-latin/noto-sans-v6-latin.css?v=') }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/css/grocy.css?v=') }}{{ $version }}" rel="stylesheet">
	@stack('pageStyles')

	<script>
		var Grocy = { };
		Grocy.Components = { };
		Grocy.BaseUrl = '{{ $U('/') }}';
		Grocy.LocalizationStrings = {!! json_encode($localizationStrings) !!};
		Grocy.ActiveNav = '@yield('activeNav', '')';
	</script>
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-mobile" >
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ $U('/') }}">grocy</a>
			</div>

			<div id="navbar" class="navbar-collapse collapse">
				@include('components.usermenu')
			</div>

			<div id="navbar-mobile" class="navbar-collapse collapse">
				@include('components.menu')
				@include('components.usermenu')
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">

			<div id="sidebar" class="col-sm-3 col-lg-2">
				<nav class="navbar navbar-default navbar-fixed-side hidden-xs">
					<div class="navbar-collapse collapse">
						@include('components.menu')
					</div>
				</nav>
			</div>

			<div class="col-sm-9 col-lg-10">
				@yield('content')
			</div>

		</div>
	</div>

	<div class="modal fade" id="about-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content text-center">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ $L('About grocy') }}</h4>
				</div>
				<div class="modal-body">
					grocy is a project by
					<a href="https://berrnd.de" target="_blank">Bernd Bestel</a><br>
					Created with passion since 2017<br>
					<br>
					Version {{ $version }}<br>
					{{ $L('Released on') }} {{ $releaseDate }} <time class="timeago timeago-contextual" datetime="{{ $releaseDate }}"></time><br>
					<br>
					Life runs on code<br>
					<a href="https://github.com/berrnd/grocy" target="_blank">
						<i class="fa fa-github"></i>
					</a>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ $L('Close') }}</button>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ $U('/bower_components/jquery/dist/jquery.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/bootstrap/dist/js/bootstrap.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/bootbox/bootbox.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/jquery.serializeJSON/jquery.serializejson.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js?v=') }}{{ $version }}"></script>
	@if(!empty($L('bootstrap_datepicker_locale')))<script src="{{ $U('/bower_components') }}/bootstrap-datepicker/dist/locales/bootstrap-datepicker.{{ $L('bootstrap_datepicker_locale') }}.min.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/bower_components/moment/min/moment.min.js?v=') }}{{ $version }}"></script>
	@if(!empty($L('moment_locale')))<script src="{{ $U('/bower_components') }}/moment/locale/{{ $L('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/bower_components/bootstrap-validator/dist/validator.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/bootstrap-combobox/js/bootstrap-combobox.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/datatables.net/js/jquery.dataTables.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/jquery-timeago/jquery.timeago.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components') }}/jquery-timeago/locales/jquery.timeago.{{ $L('timeago_locale') }}.js?v={{ $version }}"></script>
	<script src="{{ $U('/bower_components/toastr/toastr.min.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/tagmanager/tagmanager.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js?v=') }}{{ $version }}"></script>

	<script src="{{ $U('/js/extensions.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=') }}{{ $version }}"></script>
	@stack('pageScripts')
	@stack('componentScripts')
	<script src="{{ $U('/viewjs') }}/@yield('viewJsName').js?v={{ $version }}"></script>

	@if(file_exists(__DIR__ . '/../../data/add_before_end_body.html'))
		@php include __DIR__ . '/../../data/add_before_end_body.html' @endphp
	@endif
</body>
</html>
