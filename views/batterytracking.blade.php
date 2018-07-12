@extends('layout.default')

@section('title', $L('Battery tracking'))
@section('activeNav', 'batterytracking')
@section('viewJsName', 'batterytracking')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="batterytracking-form" novalidate>

			<div class="form-group">
				<label for="battery_id">{{ $L('Battery') }}</label>
				<select class="form-control combobox" id="battery_id" name="battery_id" required>
					<option value=""></option>
					@foreach($batteries as $battery)
						<option value="{{ $battery->id }}">{{ $battery->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('You have to select a battery') }}</div>
			</div>

			@include('components.datetimepicker', array(
				'id' => 'tracked_time',
				'label' => 'Tracked time',
				'format' => 'YYYY-MM-DD HH:mm:ss',
				'initWithNow' => true,
				'limitEndToNow' => true,
				'limitStartToNow' => false,
				'invalidFeedback' => $L('This can only be before now')
			))

			<button id="save-batterytracking-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.batterycard')
	</div>
</div>
@stop
