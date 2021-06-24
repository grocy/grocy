@extends($rootLayout)

@section('title', $__t('Calendar'))
@section('activeNav', 'calendar')
@section('viewJsName', 'calendar')

@push('pageStyles')
<link href="{{ $U('/css/viewcss/calendar.css?v=', true) }}{{ $version }}"
	rel="stylesheet">
@endpush

@section('grocyConfigProps')
	fullcalendarEventSources: {!! json_encode([ "events" => $fullcalendarEventSources]) !!},
@endsection

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fas fa-ellipsis-v"></i>
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

<div class="row">
	<div class="col">
		<div id="calendar"></div>
	</div>
</div>
@stop
