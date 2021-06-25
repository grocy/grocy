@extends($rootLayout)

@section('title', $__t('Consume'))
@section('activeNav', 'consume')
@section('viewJsName', 'consume')

@section('grocyConfigProps')
	QuantityUnits: {!! json_encode($quantityUnits) !!},
	QuantityUnitConversionsResolved: {!! json_encode($quantityUnitConversionsResolved) !!},
	DefaultMinAmount: '{{$DEFAULT_MIN_AMOUNT}}',
@endsection

@section('forceUserSettings')
	@if($embedded)
	scan_mode_consume_enabled: false,
	@endif
@endsection

@section('content')
@php
$classes = $embedded ? '' : 'col-md-6 col-xl-4';
@endphp

<div class="row">
	<div class="col-12 {{ $classes }} pb-3">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3 hide-when-embedded"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fas fa-ellipsis-v"></i>
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
				@endif
			</div>
		</div>

		<hr class="my-2">

		<form id="consume-form" data-scanmode="enabled"
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
						&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('The first item in this list would be picked by the default rule which is "Opened first, then first due first, then first in first out"') }}"></i>
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

			<button id="save-consume-button" data-scanmode="submit"
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
