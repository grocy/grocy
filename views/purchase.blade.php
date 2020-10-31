@extends('layout.default')

@section('title', $__t('Purchase'))
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@push('pageScripts')
<script src="{{ $U('/js/grocy_uisound.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				@if(!$embedded)
				<button id="scan-mode-button"
					class="btn @if(boolval($userSettings['scan_mode_purchase_enabled'])) btn-success @else btn-danger @endif"
					type="checkbox">{{ $__t('Scan mode') }} <span id="scan-mode-status">@if(boolval($userSettings['scan_mode_purchase_enabled'])) {{ $__t('on') }} @else {{ $__t('off') }} @endif</span></button>
				<input id="scan-mode"
					type="checkbox"
					class="d-none user-setting-control"
					data-setting-key="scan_mode_purchase_enabled"
					@if(boolval($userSettings['scan_mode_purchase_enabled']))
					checked
					@endif>
				@else
				<script>
					Grocy.UserSettings.scan_mode_purchase_enabled = false;
				</script>
				@endif
			</div>
		</div>

		<hr class="my-2 py-1">

		<form id="purchase-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#amount'
			))

			@include('components.numberpicker', array(
			'id' => 'amount',
			'label' => 'Amount',
			'hintId' => 'amount_qu_unit',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts'] - 1) . '1',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@if(boolval($userSettings['show_purchased_date_on_purchase']))
			@include('components.datetimepicker2', array(
			'id' => 'purchased_date',
			'label' => 'Purchased date',
			'format' => 'YYYY-MM-DD',
			'initWithNow' => true,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A purchased date is required'),
			'nextInputSelector' => '#best_before_date',
			'additionalCssClasses' => 'date-only-datetimepicker2',
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			@include('components.datetimepicker', array(
			'id' => 'best_before_date',
			'label' => 'Best before',
			'format' => 'YYYY-MM-DD',
			'initWithNow' => false,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A best before date is required'),
			'nextInputSelector' => '#price',
			'additionalCssClasses' => 'date-only-datetimepicker',
			'shortcutValue' => '2999-12-31',
			'shortcutLabel' => 'Never expires',
			'earlierThanInfoLimit' => date('Y-m-d'),
			'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
			'id' => 'price',
			'label' => 'Price',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices'] - 1) . '1',
			'decimals' => $userSettings['stock_decimal_places_prices'],
			'value' => '',
			'hintId' => 'price-hint',
			'invalidFeedback' => $__t('The price cannot be lower than %s', '0'),
			'isRequired' => false,
			'additionalGroupCssClasses' => 'mb-1'
			))

			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input"
					type="radio"
					name="price-type"
					id="price-type-unit-price"
					value="unit-price"
					checked>
				<label class="form-check-label"
					for="price-type-unit-price">{{ $__t('Price') }}</label>
			</div>
			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input"
					type="radio"
					name="price-type"
					id="price-type-total-price"
					value="total-price">
				<label class="form-check-label"
					for="price-type-total-price">{{ $__t('Total price') }}</label>
			</div>
			@include('components.shoppinglocationpicker', array(
			'label' => 'Store',
			'shoppinglocations' => $shoppinglocations
			))
			@else
			<input type="hidden"
				name="price"
				id="price"
				value="0">
			@endif

			@include('components.numberpicker', array(
			'id' => 'qu_factor_purchase_to_stock',
			'label' => 'Factor purchase to stock quantity unit',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts'] - 1) . '1',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'additionalGroupCssClasses' => 'd-none',
			'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
			'additionalCssClasses' => 'input-group-qu',
			'additionalHtmlElements' => '<p id="qu-conversion-info"
				class="form-text text-muted small d-none"></p>'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
			'locations' => $locations,
			'isRequired' => false
			))
			@endif

			<button id="save-purchase-button"
				class="btn btn-success d-block">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
