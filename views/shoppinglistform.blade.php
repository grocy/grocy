@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit shopping list item'))
@else
	@section('title', $L('Create shopping list item'))
@endif

@section('viewJsName', 'shoppinglistform')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $listItem->id }};</script>
		@endif

		<form id="shoppinglist-form" novalidate>

			<div class="form-group">
				<label for="product_id">{{ $L('Product') }}&nbsp;&nbsp;<i class="fas fa-barcode"></i></label>
				<select class="form-control combobox" id="product_id" name="product_id" value="@if($mode == 'edit') {{ $listItem->product_id }} @endif">
					<option value=""></option>
					@foreach($products as $product)
						<option @if($mode == 'edit' && $product->id == $listItem->product_id) selected="selected" @endif data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<label for="amount">{{ $L('Amount') }}&nbsp;&nbsp;<span id="amount_qu_unit" class="small text-muted"></span><br><span class="small text-muted">@if($mode == 'edit' && $listItem->amount_autoadded > 0){{ $L('#1 units were automatically added and will apply in addition to the amount entered here', $listItem->amount_autoadded) }}@endif</span></label>
				<input type="number" class="form-control" id="amount" name="amount" value="@if($mode == 'edit'){{ $listItem->amount }}@else{{1}}@endif" min="0" required>
				<div class="invalid-feedback">{{ $L('This cannot be negative') }}</div>
			</div>

			<div class="form-group">
				<label for="note">{{ $L('Note') }}</label>
				<textarea class="form-control" rows="2" id="note" name="note">@if($mode == 'edit'){{ $listItem->note }}@endif</textarea>
			</div>

			<button id="save-shoppinglist-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.productcard')
	</div>
</div>
@stop
