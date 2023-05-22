@extends('layout.default')

@section('title', $__t('Purchase'))

@push('pageScripts')
<script src="{{ $U('/js/grocy_uisound.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
	Grocy.DefaultMinAmount = '{{ $DEFAULT_MIN_AMOUNT }}';
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
					class="btn @if(boolval($userSettings['scan_mode_purchase_enabled'])) btn-success @else btn-danger @endif m-1 mt-md-0 mb-md-0 float-right"
					data-toggle="tooltip"
					title="{{ $__t('When enabled, after changing/scanning a product and if all fields could be automatically populated (by product and/or barcode defaults), the transaction is automatically submitted') }}">{{ $__t('Scan mode') }} <span id="scan-mode-status">@if(boolval($userSettings['scan_mode_purchase_enabled'])) {{ $__t('on') }} @else {{ $__t('off') }} @endif</span></button>
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

		<hr class="my-2">

		<form id="purchase-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#display_amount'
			))

			@include('components.productamountpicker', array(
			'value' => 1,
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
			'label' => 'Due date',
			'format' => 'YYYY-MM-DD',
			'initWithNow' => false,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A due date is required'),
			'nextInputSelector' => '#price',
			'additionalCssClasses' => 'date-only-datetimepicker',
			'shortcutValue' => '2999-12-31',
			'shortcutLabel' => 'Never overdue',
			'earlierThanInfoLimit' => date('Y-m-d'),
			'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
			'id' => 'price',
			'label' => 'Price',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices_input']),
			'decimals' => $userSettings['stock_decimal_places_prices_input'],
			'value' => '',
			'contextInfoId' => 'price-hint',
			'isRequired' => false,
			'additionalGroupCssClasses' => 'mb-1',
			'additionalCssClasses' => 'locale-number-input locale-number-currency'
			))

			<div class="custom-control custom-radio custom-control-inline mt-n2 mb-3">
				<input class="custom-control-input"
					type="radio"
					name="price-type"
					id="price-type-unit-price"
					value="unit-price"
					checked
					tabindex="-1">
				<label class="custom-control-label"
					for="price-type-unit-price">{{ $__t('Unit price') }}</label>
			</div>
			<div class="custom-control custom-radio custom-control-inline mt-n2 mb-3">
				<input class="custom-control-input"
					type="radio"
					name="price-type"
					id="price-type-total-price"
					value="total-price"
					tabindex="-1">
				<label class="custom-control-label"
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

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
			'locations' => $locations,
			'isRequired' => false
			))
			@endif

			@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
			<div class="form-group">
				<label for="stock_label_type">{{ $__t('Stock entry label') }}</label>
				<select class="custom-control custom-select"
					id="stock_label_type"
					name="stock_label_type">
					<option value="0">{{ $__t('No label') }}</option>
					<option value="1">{{ $__t('Single label') }}</option>
					<option value="2">{{ $__t('Label per unit') }}</option>
				</select>
				<div id="stock-entry-label-info"
					class="form-text text-info"></div>
			</div>
			@endif

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<div class="input-group">
					<input type="text"
						class="form-control"
						id="note"
						name="note">
				</div>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'stock'
			))

			<button id="save-purchase-button"
				class="btn btn-success d-block">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
