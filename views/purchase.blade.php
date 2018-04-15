@extends('layout.default')

@section('title', 'Purchase')
@section('activeNav', 'purchase')
@section('viewJsName', 'purchase')

@section('content')
<div class="col-sm-4 col-sm-offset-3 col-md-3 col-md-offset-2">

	<h1 class="page-header">Purchase</h1>

	<form id="purchase-form">

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

		@include('components.datepicker', array(
			'id' => 'best_before_date',
			'label' => 'Best before'
		))

		<div class="form-group">
			<label for="amount">Amount&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
			<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
			<div class="help-block with-errors"></div>
		</div>

		<button id="save-purchase-button" type="submit" class="btn btn-default">OK</button>

	</form>

</div>

<div class="col-sm-6 col-md-5 col-lg-3">
	@include('components.productcard')
</div>
@stop
