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

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" step="1.00" required>
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '1') }}</div>
			</div>

			<div class="form-group">
				<label for="price">{{ $L('Price') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted">{{ $L('in #1 per purchase quantity unit', GROCY_CURRENCY) }}</span></label>
				<input type="number" class="form-control" id="price" name="price" min="0" step="0.01">
				<div class="invalid-feedback">{{ $L('The price cannot be lower than #1', '0') }}</div>
			</div>

			<button id="save-purchase-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
