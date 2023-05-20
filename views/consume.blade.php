@extends('layout.default')

@section('title', $__t('Consume'))

@push('pageScripts')
<script src="{{ $U('/js/grocy_uisound.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
	Grocy.DefaultMinAmount = '{{$DEFAULT_MIN_AMOUNT}}';
</script>

<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3 hide-when-embedded"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				@if(!$embedded)
				<button id="scan-mode-button"
					class="btn @if(boolval($userSettings['scan_mode_consume_enabled'])) btn-success @else btn-danger @endif m-1 mt-md-0 mb-md-0 float-right"
					data-toggle="tooltip"
					title="{{ $__t('When enabled, after changing/scanning a product and if all fields could be automatically populated (by product and/or barcode defaults), the transaction is automatically submitted') }}">{{ $__t('Scan mode') }} <span id="scan-mode-status">@if(boolval($userSettings['scan_mode_consume_enabled'])) {{ $__t('on') }} @else {{ $__t('off') }} @endif</span></button>
				<input id="scan-mode"
					type="checkbox"
					class="d-none user-setting-control"
					data-setting-key="scan_mode_consume_enabled"
					@if(boolval($userSettings['scan_mode_consume_enabled']))
					checked
					@endif>
				@else
				<script>
					Grocy.UserSettings.scan_mode_consume_enabled = false;
				</script>
				@endif
			</div>
		</div>

		<hr class="my-2">

		<form id="consume-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#amount',
			'disallowAddProductWorkflows' => true
			))

			<div id="consume-exact-amount-group"
				class="form-group d-none">
				<div class="custom-control custom-checkbox">
					<input class="form-check-input custom-control-input"
						type="checkbox"
						id="consume-exact-amount"
						name="consume-exact-amount"
						value="1">
					<label class="form-check-label custom-control-label"
						for="consume-exact-amount">{{ $__t('Consume exact amount') }}
					</label>
				</div>
			</div>

			@include('components.productamountpicker', array(
			'value' => 1,
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			<div class="form-group @if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">
				<label for="location_id">{{ $__t('Location') }}</label>
				<select required
					class="custom-control custom-select location-combobox"
					id="location_id"
					name="location_id">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input class="form-check-input custom-control-input"
						type="checkbox"
						id="spoiled"
						name="spoiled"
						value="1">
					<label class="form-check-label custom-control-label"
						for="spoiled">{{ $__t('Spoiled') }}
					</label>
				</div>
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

			@if (GROCY_FEATURE_FLAG_RECIPES)
			@include('components.recipepicker', array(
			'recipes' => $recipes,
			'isRequired' => false,
			'hint' => $__t('This is for statistical purposes only')
			))
			@endif

			<button id="save-consume-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<button id="save-mark-as-open-button"
				class="btn btn-secondary permission-STOCK_OPEN">{{ $__t('Mark as opened') }}</button>
			@endif

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
