@extends('layout.default')

@section('title', 'Consume')
@section('activeNav', 'consume')
@section('viewJsName', 'consume')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-3 col-md-offset-2 main">

	<h1 class="page-header">Consume</h1>

	<form id="consume-form">

		<div class="form-group">
			<label for="product_id">Product&nbsp;&nbsp;<i class="fa fa-barcode"></i></label>
			<select class="form-control combobox" id="product_id" name="product_id" required>
				<option value=""></option>
				@foreach($products as $product)
					<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
				@endforeach
			</select>
			<div id="product-error" class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="amount">Amount&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span></label>
			<input type="number" class="form-control" id="amount" name="amount" value="1" min="1" required>
			<div class="help-block with-errors"></div>
		</div>

		<div class="checkbox">
			<label for="spoiled">
				<input type="checkbox" id="spoiled" name="spoiled"> Spoiled
			</label>
		</div>

		<button id="save-consume-button" type="submit" class="btn btn-default">OK</button>

	</form>

</div>

<div class="col-sm-6 col-md-5 col-lg-3 main well">
	<h3>Product overview <strong><span id="selected-product-name"></span></strong></h3>
	<h4><strong>Stock quantity unit:</strong> <span id="selected-product-stock-qu-name"></span></h4>

	<p>
		<strong>Stock amount:</strong> <span id="selected-product-stock-amount"></span> <span id="selected-product-stock-qu-name2"></span><br>
		<strong>Last purchased:</strong> <span id="selected-product-last-purchased"></span> <time id="selected-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong>Last used:</strong> <span id="selected-product-last-used"></span> <time id="selected-product-last-used-timeago" class="timeago timeago-contextual"></time>
	</p>
</div>
@stop
