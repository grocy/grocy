@extends('layout.default')

@section('title', 'Inventory')
@section('activeNav', 'inventory')
@section('viewJsName', 'inventory')

@section('content')
<div class="col-sm-4 col-sm-offset-3 col-md-3 col-md-offset-2">

	<h1 class="page-header">Inventory</h1>

	<form id="inventory-form">

		<div class="form-group">
			<label for="product_id">Product&nbsp;&nbsp;<i class="fa fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted hide">&nbsp;&nbsp;Barcode lookup is disabled</span></label>
			<select class="form-control combobox" id="product_id" name="product_id" required>
				<option value=""></option>
				@foreach($products as $product)
					<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
				@endforeach
			</select>
			<div class="help-block with-errors"></div>
			<div id="flow-info-addbarcodetoselection" class="text-muted small hide"><strong><span id="addbarcodetoselection"></span></strong> will be added to the list of barcodes for the selected product on submit.</div>
		</div>

		<div class="form-group">
			<label for="new_amount">New amount&nbsp;&nbsp;<span id="new_amount_qu_unit" class="small text-muted"></span></label>
			<input type="number" data-notequal="notequal" class="form-control" id="new_amount" name="new_amount" min="0" not-equal="-1" required>
			<div class="help-block with-errors"></div>
			<div id="inventory-change-info" class="help-block text-muted"></div>
		</div>

		@include('components.datepicker', array(
			'id' => 'best_before_date',
			'label' => 'Best before&nbsp;&nbsp;<span class="small text-muted">This will apply to added products</span>'
		))

		<button id="save-inventory-button" type="submit" class="btn btn-default">OK</button>

	</form>

</div>

<div class="col-sm-6 col-md-5 col-lg-3">
	@include('components.productcard')
</div>
@stop
