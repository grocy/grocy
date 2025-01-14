@extends('layout.default')

@section('title', $__t('Edit stock entry'))

@section('content')
<script>
	Grocy.EditObjectId = "{{ $stockEntry->stock_id }}";
	Grocy.EditObjectRowId = {{ $stockEntry->id }};
	Grocy.EditObjectProductId = {{ $stockEntry->product_id }};
</script>

<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-12 col-md-6 col-xl-4 pb-3">

		<form id="stockentry-form"
			novalidate>
			@php
			$product = FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id);
			@endphp

			@include('components.numberpicker', array(
			'id' => 'amount',
			'value' => $stockEntry->amount,
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'label' => 'Amount',
			'contextInfoId' => 'amount_qu_unit',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

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
			'label' => 'Due date',
			'format' => 'YYYY-MM-DD',
			'initWithNow' => false,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A due date is required'),
			'nextInputSelector' => '#best_before_date',
			'additionalGroupCssClasses' => 'date-only-datetimepicker',
			'shortcutValue' => '2999-12-31',
			'shortcutLabel' => 'Never overdue',
			'earlierThanInfoLimit' => date('Y-m-d'),
			'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
			'additionalGroupCssClasses' => $additionalGroupCssClasses,
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@php $additionalGroupCssClasses = ''; @endphp

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@php
			if (empty($stockEntry->price))
			{
			$price = '';
			}
			else
			{
			$price = $stockEntry->price;
			}
			@endphp
			@include('components.numberpicker', array(
			'id' => 'price',
			'value' => $price,
			'label' => 'Price',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices_input']),
			'decimals' => $userSettings['stock_decimal_places_prices_input'],
			'hint' => $__t('Per stock quantity unit'),
			'isRequired' => false,
			'additionalCssClasses' => 'locale-number-input locale-number-currency'
			))
			@include('components.shoppinglocationpicker', array(
			'label' => 'Store',
			'shoppinglocations' => $shoppinglocations,
			'prefillById' => $stockEntry->shopping_location_id
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
			'prefillById' => $stockEntry->location_id
			))
			@else
			<input type="hidden"
				name="location_id"
				id="location_id"
				value="1">
			@endif

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<div class="input-group">
					<input type="text"
						class="form-control"
						id="note"
						name="note"
						value="{{ $stockEntry->note }}">
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($stockEntry->open == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="open" name="open" value="1">
					<label class="form-check-label custom-control-label"
						for="open">{{ $__n(1, 'Opened', 'Opened') }}</label>
				</div>
			</div>
			@endif

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'stock'
			))

			@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input class="form-check-input custom-control-input"
						type="checkbox"
						id="print-label"
						value="1">
					<label class="form-check-label custom-control-label"
						for="print-label">{{ $__t('Reprint stock entry label') }}</label>
				</div>
			</div>
			@endif

			<button id="save-stockentry-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>
</div>
@stop
