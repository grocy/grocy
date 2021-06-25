@extends($rootLayout)

@section('title', $__t('Edit stock entry'))
@section('viewJsName', 'stockentryform')

@section('grocyConfigProps')
EditMode: "edit",
EditObjectId: {{ $stockEntry->id }},
EditObjectProductId: {{ $stockEntry->product_id }},
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

@php
$classes = $embedded ? '' : 'col-md-6 col-xl-4'
@endphp

<div class="row">
	<div class="col-12 {{ $classes }} pb-3">

		<form id="stockentry-form"
			novalidate>
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

			@include('components.numberpicker', array(
			'id' => 'amount',
			'value' => $stockEntry->amount,
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'label' => 'Amount',
			'contextInfoId' => 'amount_qu_unit',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

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
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices']),
			'decimals' => $userSettings['stock_decimal_places_prices'],
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

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($stockEntry->open == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="open" name="open" value="1">
					<label class="form-check-label custom-control-label"
						for="open">{{ $__t('Opened') }}</label>
				</div>
			</div>

			<button id="save-stockentry-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>
</div>
@stop
