@php require_frontend_packages(['bootstrap-combobox']); @endphp

@extends('layout.default')

@section('title', $__t('Chore tracking'))

@section('content')
<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">
		<h2 class="title">@yield('title')</h2>

		<hr class="my-2">

		<form id="choretracking-form"
			novalidate>

			<div class="form-group">
				<label class="w-100"
					for="chore_id">
					{{ $__t('Chore') }}
					<i id="barcode-lookup-hint"
						class="fa-solid fa-barcode float-right mt-1"></i>
				</label>
				<select class="form-control combobox barcodescanner-input"
					id="chore_id"
					name="chore_id"
					required
					data-target="@chorepicker">
					<option value=""></option>
					@foreach($chores as $chore)
					<option value="{{ $chore->id }}">{{ $chore->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('You have to select a chore') }}</div>
			</div>

			@include('components.datetimepicker', array(
			'id' => 'tracked_time',
			'label' => 'Tracked time',
			'format' => 'YYYY-MM-DD HH:mm:ss',
			'initWithNow' => true,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A date is required')
			))

			@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
			@include('components.userpicker', array(
			'label' => 'Done by',
			'users' => $users,
			'nextInputSelector' => '#user_id',
			'prefillByUserId' => GROCY_USER_ID
			))
			@else
			<input type="hidden"
				id="user_id"
				name="user_id"
				value="{{ GROCY_USER_ID }}">
			@endif

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'chores_log'
			))

			<button class="btn btn-success save-choretracking-button">{{ $__t('OK') }}</button>

			<button class="btn btn-secondary save-choretracking-button skip">{{ $__t('Skip') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4">
		@include('components.chorecard')
	</div>
</div>

@include('components.camerabarcodescanner')
@stop
