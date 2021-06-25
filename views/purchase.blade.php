@extends($rootLayout)

@section('title', $__t('Purchase'))
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@section('grocyConfigProps')
	QuantityUnits: {!! json_encode($quantityUnits) !!},
	QuantityUnitConversionsResolved: {!! json_encode($quantityUnitConversionsResolved) !!},
	DefaultMinAmount: '{{$DEFAULT_MIN_AMOUNT}}',
@endsection

@section('forceUserSettings')
	@if($embedded)
	scan_mode_purchase_enabled: false,
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
				@endif
			</div>
		</div>

		<hr class="my-2">

		<form id="purchase-form" data-scanmode="enabled"
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
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices']),
			'decimals' => $userSettings['stock_decimal_places_prices'],
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
					checked>
				<label class="custom-control-label"
					for="price-type-unit-price">{{ $__t('Unit price') }}</label>
			</div>
			<div class="custom-control custom-radio custom-control-inline mt-n2 mb-3">
				<input class="custom-control-input"
					type="radio"
					name="price-type"
					id="price-type-total-price"
					value="total-price">
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

			@if(GROCY_FEATURE_FLAG_LABELPRINTER)
			<div class="form-group">
				<label for="print_stock_label">{{ $__t('Stock entry label') }}</label>
				<select class="form-control"
					id="print_stock_label"
					name="print_stock_label">
					<option value="0">{{ $__t('No label') }}</option>
					<option value="1">{{ $__t('Single label') }}</option>
					<option value="2"
						id="label-option-per-unit">{{ $__t('Label per unit') }}</option>
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>
			@endif

			<button id="save-purchase-button" data-scanmode="submit"
				class="btn btn-success d-block">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
