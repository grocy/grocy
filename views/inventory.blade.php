@extends('layout.default')

@section('title', $L('Inventory'))
@section('activeNav', 'inventory')
@section('viewJsName', 'inventory')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4">
		<h1>@yield('title')</h1>

		<form id="inventory-form" novalidate>

			<div class="form-group">
				<label for="product_id">{{ $L('Product') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted d-none">&nbsp;&nbsp;{{ $L('Barcode lookup is disabled') }}</span></label>
				<select class="form-control combobox" id="product_id" name="product_id" required>
					<option value=""></option>
					@foreach($products as $product)
						<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('You have to select a product') }}</div>
				<div id="flow-info-addbarcodetoselection" class="form-text text-muted small d-none"><strong><span id="addbarcodetoselection"></span></strong> {{ $L('will be added to the list of barcodes for the selected product on submit') }}</div>
			</div>

			<div class="form-group">
				<label for="new_amount">{{ $L('New amount') }}&nbsp;&nbsp;<span id="new_amount_qu_unit" class="small text-muted"></span></label>
				<input type="number" data-notequal="notequal" class="form-control" id="new_amount" name="new_amount" min="0" not-equal="-1" required>
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '0') }}</div>
				<div id="inventory-change-info" class="form-text text-muted small d-none"></div>
			</div>

			@include('components.datepicker', array(
				'id' => 'best_before_date',
				'label' => 'Best before',
				'hint' => 'This will apply to added products'
			))

			<button id="save-inventory-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
