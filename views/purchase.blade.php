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
				'invalidFeedback' => $L('The amount cannot be lower than #1', '1')
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

			<div class="form-group">
				<label for="location_id">{{ $L('Location') }}</label>
				<select required class="form-control" id="location_id" name="location_id">
					<option></option>
					@foreach($locations as $location)
						<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A location is required') }}</div>
			</div>

			<button id="save-purchase-button" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
