@extends('layout.default')

@section('title', $L('Purchase'))
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="purchase-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#best_before_date .datetimepicker-input'
			))

			@include('components.datetimepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'limitEndToNow' => false,
				'limitStartToNow' => true,
				'invalidFeedback' => $L('A best before date is required and must be later than today'),
				'nextInputSelector' => '#amount',
				'additionalCssClasses' => 'date-only-datetimepicker',
				'shortcutValue' => '2999-12-31',
				'shortcutLabel' => 'Never expires'
			))

			@include('components.numberpicker', array(
				'id' => 'amount',
				'label' => 'Amount',
				'hintId' => 'amount_qu_unit',
				'min' => 1,
				'invalidFeedback' => $L('The amount cannot be lower than #1', '1'),
				'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info" class="text-info font-italic d-none">' . $L('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@include('components.numberpicker', array(
				'id' => 'price',
				'label' => 'Price',
				'min' => 0,
				'step' => 0.01,
				'value' => '',
				'hint' => $L('in #1 per purchase quantity unit', GROCY_CURRENCY),
				'invalidFeedback' => $L('The price cannot be lower than #1', '0'),
				'isRequired' => false
			))

			@include('components.locationpicker', array(
				'locations' => $locations,
				'isRequired' => false,
				'hint' => $L('This is for statistical purposes only')
			))

			<button id="save-purchase-button" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
