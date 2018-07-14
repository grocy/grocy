@extends('layout.default')

@section('title', $L('Inventory'))
@section('activeNav', 'inventory')
@section('viewJsName', 'inventory')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="inventory-form" novalidate>

			@include('components.productpicker', array(
				'products' => $products,
				'nextInputSelector' => '#new_amount'
			))

			<div class="form-group">
				<label for="new_amount">{{ $L('New amount') }}&nbsp;&nbsp;<span id="new_amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" data-notequal="notequal" class="form-control" id="new_amount" name="new_amount" min="0" not-equal="-1" required>
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '0') }}</div>
				<div id="inventory-change-info" class="form-text text-muted small d-none"></div>
			</div>
			
			@include('components.datetimepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before',
				'hint' => 'This will apply to added products',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'limitEndToNow' => false,
				'limitStartToNow' => true,
				'invalidFeedback' => $L('A best before date is required and must be later than today'),
				'nextInputSelector' => '#best_before_date',
				'additionalCssClasses' => 'date-only-datetimepicker'
			))

			<button id="save-inventory-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
