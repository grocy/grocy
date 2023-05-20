@extends('layout.default')

@section('title', $__t('Transfer'))

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">
		<h2 class="title">@yield('title')</h2>

		<hr class="my-2">

		<form id="transfer-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#location_id_from',
			'disallowAddProductWorkflows' => true
			))

			<div class="form-group">
				<label for="location_id_from">{{ $__t('From location') }}</label>
				<select required
					class="custom-control custom-select location-combobox"
					id="location_id_from"
					name="location_id_from">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}"
						data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			@include('components.productamountpicker', array(
			'value' => 1,
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			<div class="form-group">
				<label for="location_id_to">{{ $__t('To location') }}</label>
				<select required
					class="custom-control custom-select location-combobox"
					id="location_id_to"
					name="location_id_to">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}"
						data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input class="form-check-input custom-control-input"
						type="checkbox"
						id="use_specific_stock_entry"
						name="use_specific_stock_entry"
						value="1">
					<label class="form-check-label custom-control-label"
						for="use_specific_stock_entry">{{ $__t('Use a specific stock item') }}
						&nbsp;<i class="fa-solid fa-question-circle text-muted"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('The first item in this list would be picked by the default rule consume rule (Opened first, then first due first, then first in first out)') }}"></i>
					</label>
				</div>
				<select disabled
					class="custom-control custom-select mt-2"
					id="specific_stock_entry"
					name="specific_stock_entry">
					<option></option>
				</select>
			</div>

			<button id="save-transfer-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
