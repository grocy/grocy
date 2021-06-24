@extends($rootLayout)

@section('title', $__t('About grocy'))
@section('viewJsName', 'about')

@section('content')
<div class="row">
	<div class="col-12 col-md-6 text-center">
		<h2 class="title">@yield('title')</h2>

		<ul class="nav nav-tabs justify-content-center mt-3">
			<li class="nav-item">
				<a class="nav-link active"
					id="system-info-tab"
					data-toggle="tab"
					href="#system-info">{{ $__t('System info') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link"
					id="changelog-tab"
					data-toggle="tab"
					href="#changelog">{{ $__t('Changelog') }}</a>
			</li>
		</ul>

		<div class="tab-content mt-3">

			<div class="tab-pane show active"
				id="system-info">
				<p>
					Version <code>{{ $version }}</code><br>
					{{ $__t('Released on') }} <code>{{ $releaseDate }}</code> <time class="timeago timeago-contextual"
						datetime="{{ $releaseDate }}"></time>
				</p>

				<p>
					PHP Version <code>{{ $system_info['php_version'] }}</code><br>
					SQLite Version <code>{{ $system_info['sqlite_version'] }}</code>
				</p>

				<p>
					{{ $__t('Do you find grocy useful?') }}<br>
					<a class="btn btn-sm btn-primary text-white mt-1"
						href="https://grocy.info/#say-thanks"
						target="_blank">{{ $__t('Say thanks') }} <i class="fas fa-heart"></i></a>
				</p>
			</div>

			<div class="tab-pane show"
				id="changelog">
				@php $Parsedown = new Parsedown(); @endphp
				@foreach($changelog['changelog_items'] as $changelogItem)
				<div class="card my-2">
					<div class="card-header">
						<a class="discrete-link"
							data-toggle="collapse-next"
							href="#">
							Version <span class="font-weight-bold">{{ $changelogItem['version'] }}</span><br>
							{{ $__t('Released on') }} <span class="font-weight-bold">{{ $changelogItem['release_date'] }}</span>
							<time class="timeago timeago-contextual"
								datetime="{{ $changelogItem['release_date'] }}"></time>
						</a>
					</div>
					<div class="collapse @if($changelogItem['release_number'] >= $changelog['newest_release_number'] - 4) show @endif">
						<div class="card-body text-left">
							{!! $Parsedown->text($changelogItem['body']) !!}
						</div>
					</div>
				</div>
				@endforeach
			</div>

		</div>

		<p class="small text-muted">
			grocy is a project by
			<a href="https://berrnd.de"
				class="text-dark"
				target="_blank">Bernd Bestel</a><br>
			Created with passion since 2017<br>
			Life runs on code<br>
			<a href="https://github.com/grocy/grocy"
				class="text-dark"
				target="_blank">
				<i class="fab fa-github"></i>
			</a>
		</p>
	</div>
</div>
@stop
