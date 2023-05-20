@extends('layout.default')

@section('title', $__t('Calendar'))

@push('pageScripts')
<script src="{{ $U('/node_modules/fullcalendar/dist/fullcalendar.min.js?v=', true) }}{{ $version }}"></script>
@if(!empty($__t('fullcalendar_locale') && $__t('fullcalendar_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/fullcalendar/dist/locale/{{ $__t('fullcalendar_locale') }}.js?v={{ $version }}"></script>@endif
<script src="{{ $U('/node_modules/bwip-js/dist/bwip-js-min.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
<link href="{{ $U('/node_modules/fullcalendar/dist/fullcalendar.min.css?v=', true) }}{{ $version }}"
	rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100 m-1 mt-md-0 mb-md-0 float-right"
				id="related-links">
				<a id="ical-button"
					class="btn btn-outline-dark"
					href="#">
					{{ $__t('Share/Integrate calendar (iCal)') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<script>
	var fullcalendarEventSources = {!! json_encode(array($fullcalendarEventSources)) !!}
</script>

<div class="row">
	<div class="col">
		<div id="calendar"></div>
	</div>
</div>
@stop
