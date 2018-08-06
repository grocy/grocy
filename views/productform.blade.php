@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit product'))
@else
	@section('title', $L('Create product'))
@endif

@section('viewJsName', 'productform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $product->id }};</script>
		@endif

		<form id="product-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $product->name}}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			<div class="form-group tm-group">
				<label for="barcode-taginput">{{ $L('Barcode(s)') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i></label>
				<input type="text" class="form-control tm-input" id="barcode-taginput">
				<div id="barcode-taginput-container"></div>
			</div>

			<div class="form-group">
				<label for="location_id">{{ $L('Location') }}</label>
				<select required class="form-control" id="location_id" name="location_id">
					@foreach($locations as $location)
						<option @if($mode == 'edit' && $location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A location is required') }}</div>
			</div>

			<div class="form-group">
				<label for="min_stock_amount">{{ $L('Minimum stock amount') }}</label>
				<input required min="0" type="number" class="form-control" id="min_stock_amount" name="min_stock_amount" value="@if($mode == 'edit'){{ $product->min_stock_amount }}@else{{0}}@endif">
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '0') }}</div>
			</div>

			<div class="form-group">
				<label for="default_best_before_days">{{ $L('Default best before days') }}<br><span class="small text-muted">{{ $L('For purchases this amount of days will be added to today for the best before date suggestion') }} ({{ $L('-1 means that this product never expires') }})</span></label>
				<input required min="-1" type="number" class="form-control" id="default_best_before_days" name="default_best_before_days" value="@if($mode == 'edit'){{ $product->default_best_before_days }}@else{{0}}@endif">
				<div class="invalid-feedback">{{ $L('This cannot be lower than #1', '-1') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_id_purchase">{{ $L('Quantity unit purchase') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_purchase" name="qu_id_purchase">
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_purchase) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_id_stock">{{ $L('Quantity unit stock') }}</label>
				<select required class="form-control input-group-qu" id="qu_id_stock" name="qu_id_stock">
					@foreach($quantityunits as $quantityunit)
						<option @if($mode == 'edit' && $quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A quantity unit is required') }}</div>
			</div>

			<div class="form-group">
				<label for="qu_factor_purchase_to_stock">{{ $L('Factor purchase to stock quantity unit') }}</label>
				<input required min="1" type="number" class="form-control input-group-qu" id="qu_factor_purchase_to_stock" name="qu_factor_purchase_to_stock" value="@if ($mode == 'edit'){{ $product->qu_factor_purchase_to_stock }}@else{{1}}@endif">
				<div class="invalid-feedback">{{ $L('The amount cannot be lower than #1', '1') }}</div>
			</div>

			<p id="qu-conversion-info" class="form-text text-muted small d-none"></p>

			<button id="save-product-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>
		</form>
	</div>
</div>
@stop
