@extends('layout.default')

@section('title', $L('About grocy'))

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 text-center">
		<h1>@yield('title')</h1>

		<p class="font-italic">
			grocy is a project by
			<a href="https://berrnd.de" class="discrete-link" target="_blank">Bernd Bestel</a><br>
			Created with passion since 2017
		</p>

		<p>
			Version <code>{{ $version }}</code><br>
			{{ $L('Released on') }} <code>{{ $releaseDate }}</code> <time class="timeago timeago-contextual" datetime="{{ $releaseDate }}"></time>
		</p>

		<p>
			PHP Version <code>{{ $system_info['php_version'] }}</code><br>
			SQLite Version <code>{{ $system_info['sqlite_version'] }}</code>
		</p>
		
		<p class="small text-muted">
			Life runs on code<br>
			<a href="https://github.com/grocy/grocy" class="discrete-link" target="_blank">
				<i class="fab fa-github"></i>
			</a>
		</p>
	</div>
</div>
@stop
