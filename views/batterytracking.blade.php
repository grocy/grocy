@php require_frontend_packages(['bootstrap-combobox']); @endphp

@extends('layout.default')

@section('title', $__t('Battery tracking'))

@section('content')
<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">
		<h2 class="title">@yield('title')</h2>

		<hr class="my-2">

		<form id="batterytracking-form"
			novalidate>

			<div class="form-group">
				<label class="w-100"
					for="battery_id">
					{{ $__t('Battery') }}
					<i id="barcode-lookup-hint"
						class="fa-solid fa-barcode float-right mt-1"></i>
				</label>
				<select class="form-control combobox barcodescanner-input"
					id="battery_id"
					name="battery_id"
					required
					data-target="@batterypicker">
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

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'battery_charge_cycles'
			))

			<button id="save-batterytracking-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4">
		@include('components.batterycard')
	</div>
</div>

@include('components.camerabarcodescanner')
@stop
