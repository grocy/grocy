@extends($rootLayout)

@section('title', $__t('Battery tracking'))
@section('activeNav', 'batterytracking')
@section('viewJsName', 'batterytracking')

@section('content')
<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">
		<h2 class="title">@yield('title')</h2>

		<hr class="my-2">

		<form id="batterytracking-form"
			novalidate>

			<div class="form-group">
				<label for="battery_id">{{ $__t('Battery') }}</label>
				<select class="form-control combobox"
					id="battery_id"
					name="battery_id"
					required>
					<option value=""></option>
					@foreach($batteries as $battery)
					<option value="{{ $battery->id }}">{{ $battery->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('You have to select a battery') }}</div>
			</div>

			@include('components.datetimepicker', array(
			'id' => 'tracked_time',
			'label' => 'Tracked time',
			'format' => 'YYYY-MM-DD HH:mm:ss',
			'initWithNow' => true,
			'limitEndToNow' => true,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('This can only be before now')
			))

			<button id="save-batterytracking-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4">
		@include('components.batterycard')
	</div>
</div>
@stop
