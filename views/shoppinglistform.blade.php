@extends('layout.default')

@if($mode == 'edit')
	@section('title', 'Edit shopping list item')
@else
	@section('title', 'Create shopping list item')
@endif

@section('viewJsName', 'shoppinglistform')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-3 col-md-offset-2">

	<h1 class="page-header">@yield('title')</h1>

	<script>Grocy.EditMode = '{{ $mode }}';</script>

	@if($mode == 'edit')
		<script>Grocy.EditObjectId = {{ $listItem->id }};</script>
	@endif

	<form id="shoppinglist-form">

		<div class="form-group">
			<label for="product_id">Product&nbsp;&nbsp;<i class="fa fa-barcode"></i></label>
			<select class="form-control combobox" id="product_id" name="product_id" value="@if($mode == 'edit') {{ $listItem->product_id }} @endif">
				<option value=""></option>
				@foreach($products as $product)
					<option @if($mode == 'edit' && $product->id == $listItem->product_id) selected="selected" @endif data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
				@endforeach
			</select>
			<div id="product-error" class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="amount">Amount&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span><br><span class="small text-warning">@if($mode == 'edit' && $listItem->amount_autoadded > 0){{ $listItem->amount_autoadded }} units were automatically added and will apply in addition to the amount entered here.@endif</span></label>
			<input type="number" class="form-control" id="amount" name="amount" value="@if($mode == 'edit'){{ $listItem->amount }}@else{{1}}@endif" min="0" required>
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="note">Note</label>
			<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $listItem->note }}@endif</textarea>
		</div>

		<button id="save-shoppinglist-button" type="submit" class="btn btn-default">Save</button>

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
