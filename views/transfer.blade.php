@extends('layout.default')

@section('title', $__t('Transfer'))
@section('activeNav', 'transfer')
@section('viewJsName', 'transfer')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h2 class="title">@yield('title')</h2>
		<hr>

		<form id="transfer-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#location_id_from',
				'disallowAddProductWorkflows' => true
			))

			@php /*@include('components.locationpicker', array(
				'id' => 'location_from',
				'locations' => $locations,
				'isRequired' => true,
				'label' => 'Transfer From Location'
			))*/ @endphp

			<div class="form-group">
				<label for="location_id_from">{{ $__t('From location') }}</label>
				<select required class="form-control location-combobox" id="location_id_from" name="location_id_from">
					<option></option>
					@foreach($locations as $location)
						<option value="{{ $location->id }}" data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'value' => 1,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			<div class="form-group">
				<label for="use_specific_stock_entry">
					<input type="checkbox" id="use_specific_stock_entry" name="use_specific_stock_entry"> {{ $__t('Use a specific stock item') }}
					<span class="small text-muted">{{ $__t('The first item in this list would be picked by the default rule which is "First expiring first, then first in first out"') }}</span>
				</label>
				<select disabled class="form-control" id="specific_stock_entry" name="specific_stock_entry">
					<option></option>
				</select>
			</div>
			
			@php /*@include('components.locationpicker', array(
				'locations' => $locations,
				'isRequired' => true,
				'label' => 'Transfer to Location'
			))*/ @endphp

			<div class="form-group">
				<label for="location_id_to">{{ $__t('To location') }}</label>
				<select required class="form-control location-combobox" id="location_id_to" name="location_id_to">
					<option></option>
					@foreach($locations as $location)
						<option value="{{ $location->id }}" data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			<button id="save-transfer-button" class="btn btn-success">{{ $__t('OK') }}</button>
			
		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
