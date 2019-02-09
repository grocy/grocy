@extends('layout.default')

@section('title', $L('About grocy'))

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 text-center">
		<h1>@yield('title')</h1>

		grocy is a project by
		<a href="https://berrnd.de" class="discrete-link" target="_blank">Bernd Bestel</a><br>
		Created with passion since 2017<br>
		<br>
		Version {{ $version }}<br>
		{{ $L('Released on') }} {{ $releaseDate }} <time class="timeago timeago-contextual" datetime="{{ $releaseDate }}"></time><br>
		<br>
		PHP Version {{ $system_info['php_version'] }}<br>
		SQLite Version {{ $system_info['sqlite_version'] }}<br>
		<br>
		Life runs on code<br>
		<a href="https://github.com/grocy/grocy" class="discrete-link" target="_blank">
			<i class="fab fa-github"></i>
		</a>
	</div>
</div>
@stop
