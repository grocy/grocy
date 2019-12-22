@extends('layout.default')

@section('title', $__t('Edit stock entry'))
@section('activeNav', 'stockedit')
@section('viewJsName', 'stockedit')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="stockedit-form" novalidate>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
				'locations' => $locations
			))
			@else
			<input type="hidden" name="location_id" id="location_id" value="1">
			@endif

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'additionalAttributes' => 'data-not-equal="-1"',
				'additionalHtmlElements' => '<div id="stockedit-change-info" class="form-text text-muted small d-none"></div>',
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-small text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
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
				'nextInputSelector' => '#best_before_date',
				'additionalGroupCssClasses' => 'date-only-datetimepicker',
				'shortcutValue' => '2999-12-31',
				'shortcutLabel' => 'Never expires',
				'earlierThanInfoLimit' => date('Y-m-d'),
				'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
				'additionalGroupCssClasses' => $additionalGroupCssClasses
			))
			@php $additionalGroupCssClasses = ''; @endphp

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
				'id' => 'price',
				'label' => 'Price',
				'min' => 0,
				'step' => 0.01,
				'value' => '',
				'hint' => $__t('in %s per purchase quantity unit', GROCY_CURRENCY),
				'invalidFeedback' => $__t('The price cannot be lower than %s', '0'),
				'isRequired' => false
			))
			@else
			<input type="hidden" name="price" id="price" value="0">
			@endif

			<button id="save-stockedit-button" class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
