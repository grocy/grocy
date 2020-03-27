@extends('layout.default')

@section('title', $__t('Edit stock entry'))
@section('viewJsName', 'stockentryform')

@section('content')
<script>
	Grocy.EditObjectId = {{ $stockEntry->id }};
	Grocy.EditObjectProductId = {{ $stockEntry->product_id }};
</script>

<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="stockentry-form" novalidate>
			@php
			$product = FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id);
			@endphp

			@php
				$additionalGroupCssClasses = '';
				if (!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				{
					$additionalGroupCssClasses = 'd-none';
				}
			@endphp
			@include('components.datetimepicker', array(
				'id' => 'best_before_date',
				'initialValue' => $stockEntry->best_before_date,
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
				'additionalGroupCssClasses' => $additionalGroupCssClasses,
				'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@php $additionalGroupCssClasses = ''; @endphp

			@include('components.numberpicker', array(
				'id' => 'amount',
				'value' => $stockEntry->amount,
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '0'),
				'additionalAttributes' => 'data-not-equal="-1"',
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-small text-info font-italic d-none">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
				'id' => 'price',
				'value' => $stockEntry->price,
				'label' => 'Price',
				'min' => 0,
				'step' => 0.01,
				'hint' => $__t('in %s per purchase quantity unit', GROCY_CURRENCY),
				'invalidFeedback' => $__t('The price cannot be lower than %s', '0'),
				'isRequired' => false
			))
			@include('components.shoppinglocationpicker', array(
				'label' => 'Store',
				'shoppinglocations' => $shoppinglocations,
				'prefillById' => $stockEntry->shopping_location_id
			))
			@else
			<input type="hidden" name="price" id="price" value="0">
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
				'locations' => $locations,
				'prefillById' => $stockEntry->location_id
			))
			@else
			<input type="hidden" name="location_id" id="location_id" value="1">
			@endif
			
			@include('components.datetimepicker2', array(
				'id' => 'purchase_date',
				'initialValue' => $stockEntry->purchased_date,
				'label' => 'Purchased date',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'limitEndToNow' => false,
				'limitStartToNow' => false,
				'invalidFeedback' => $__t('A purchased date is required'),
				'nextInputSelector' => '#save-stockentry-button',
				'additionalGroupCssClasses' => 'date-only-datetimepicker'
			))

			<div class="checkbox">
				<label for="open">
					<input @if($stockEntry->open == 1) checked @endif type="checkbox" id="open" name="open"> {{ $__t('Opened') }}
				</label>
			</div>

			<button id="save-stockentry-button" class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>
</div>
@stop
