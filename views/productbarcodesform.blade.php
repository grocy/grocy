@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit Barcodes'))
@else
	@section('title', $__t('Create Barcodes'))
@endif

@section('viewJsName', 'productbarcodesform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
	
		<h3 class="text-muted">{{ $__t('Barcode for product') }} <strong>{{ $product->name }}</strong></h3>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $barcode->id }};</script>
		@endif

		<form id="barcode-form" novalidate>

			<input type="hidden" name="product_id" value="{{ $product->id }}">

			<div class="form-group">
				<label for="name">{{ $__t('Barcode') }}<i class="fas fa-barcode"></i></label>
				 <div class="input-group">
					<input type="text" class="form-control barcodescanner-input" required id="barcode" name="barcode" value="@if($mode == 'edit'){{ $barcode->barcode }}@endif" data-target="#scanned_barcode">
					@include('components.barcodescanner')
				</div>
			</div>

			@php if($mode == 'edit') { $value = $barcode->qu_factor_purchase_to_stock; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
				'id' => 'qu_factor_purchase_to_stock',
				'label' => 'Factor purchase to stock quantity unit',
				'min' => 1,
				'value' => $value,
				'isRequired' => true,
				'invalidFeedback' => $__t('The amount cannot be lower than %s', '1'),
				'additionalCssClasses' => 'input-group-qu',
			))

			@if(GROCY_FEATURE_FLAG_PRICE_TRACKING)
			<div class="form-group">
				<label for="shopping_location_id_id">{{ $__t('Default store') }}</label>
				<select class="form-control" id="shopping_location_id" name="shopping_location_id">
					<option></option>
					@foreach($shoppinglocations as $store)
						<option @if($mode == 'edit' && $store->id == $product->shopping_location_id) selected="selected" @endif value="{{ $store->id }}">{{ $store->name }}</option>
					@endforeach
				</select>
			</div>
			@else
			<input type="hidden" name="shopping_location_id" id="shopping_location_id" value="1">
			@endif

			<button id="save-barcode-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
