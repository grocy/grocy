@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit battery'))
@else
@section('title', $__t('Create battery'))
@endif

@section('viewJsName', 'batteryform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
@if($mode == 'edit')	
EditObjectId: {{ $battery->id }},
@endif
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

@php
$classes = $embedded ? '' : 'col-lg-6';
@endphp

<div class="row">
	<div class="{{ $classes }} col-12">
		<form id="battery-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $battery->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$battery->active == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="active" name="active" value="1">
					<label class="form-check-label custom-control-label"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<input type="text"
					class="form-control"
					id="description"
					name="description"
					value="@if($mode == 'edit'){{ $battery->description }}@endif">
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Used in') }}</label>
				<input type="text"
					class="form-control"
					id="used_in"
					name="used_in"
					value="@if($mode == 'edit'){{ $battery->used_in }}@endif">
			</div>

			@php if($mode == 'edit') { $value = $battery->charge_interval_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'charge_interval_days',
			'label' => 'Charge cycle interval (days)',
			'value' => $value,
			'min' => '0',
			'hint' => $__t('0 means suggestions for the next charge cycle are disabled')
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'batteries'
			))

			<button id="save-battery-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
