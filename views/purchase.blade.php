@extends('layout.default')

@section('title', $__t('Purchase'))
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@push('pageStyles')
	<link href="{{ $U('/node_modules/bootstrap-switch-button/css/bootstrap-switch-button.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@push('pageScripts')
	<script src="{{ $U('/node_modules/bootstrap-switch-button/js/bootstrap-switch-button.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_uisound.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>
			@yield('title')
			<input @if(boolval($userSettings['scan_mode_purchase_enabled'])) checked @endif id="scan-mode" type="checkbox" data-setting-key="scan_mode_purchase_enabled" data-toggle="switchbutton" data-onlabel="{{ $__t('Scan mode') }} {{ $__t('on') }}" data-offlabel="{{ $__t('Scan mode') }} {{ $__t('off') }}" data-onstyle="success" data-offstyle="primary" data-style="ml-2" data-width="180">
		</h1>

		<form id="purchase-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#best_before_date .datetimepicker-input'
			))

			@php
				$additionalGroupCssClasses = '';
				if (!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				{
					$additionalGroupCssClasses = 'd-none';
				}
			@endphp
			@include('components.datetimepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'limitEndToNow' => false,
				'limitStartToNow' => false,
				'invalidFeedback' => $__t('A best before date is required'),
				'nextInputSelector' => '#amount',
				'additionalCssClasses' => 'date-only-datetimepicker',
				'shortcutValue' => '2999-12-31',
				'shortcutLabel' => 'Never expires',
				'earlierThanInfoLimit' => date('Y-m-d'),
				'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
				'additionalGroupCssClasses' => $additionalGroupCssClasses
			))
			@php $additionalGroupCssClasses = ''; @endphp

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
				'id' => 'price',
				'label' => 'Price',
				'min' => 0,
				'step' => 0.01,
				'value' => '',
				'hint' => $__t('in %s and based on the purchase quantity unit', GROCY_CURRENCY),
				'invalidFeedback' => $__t('The price cannot be lower than %s', '0'),
				'isRequired' => false,
				'additionalGroupCssClasses' => 'mb-1'
			))
			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input" type="radio" name="price-type" id="price-type-unit-price" value="unit-price" checked>
				<label class="form-check-label" for="price-type-unit-price">{{ $__t('Unit price') }}</label>
			</div>
			<div class="form-check form-check-inline mb-3">
				<input class="form-check-input" type="radio" name="price-type" id="price-type-total-price" value="total-price">
				<label class="form-check-label" for="price-type-total-price">{{ $__t('Total price') }}</label>
			</div>
			@else
			<input type="hidden" name="price" id="price" value="0">
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
				'locations' => $locations,
				'isRequired' => false
			))
			@else
			<input type="hidden" name="location_id" id="location_id" value="1">
			@endif

			<button id="save-purchase-button" class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
