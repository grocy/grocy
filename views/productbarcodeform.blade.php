@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit Barcode'))
@else
@section('title', $__t('Create Barcode'))
@endif

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">
				@yield('title')<br>
				<span class="text-muted small">{{ $__t('Barcode for product') }} <strong>{{ $product->name }}</strong></span>
			</h2>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">

		<script>
			Grocy.EditMode = '{{ $mode }}';
			Grocy.EditObjectProduct = {!! json_encode($product) !!};
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $barcode->id }};
			Grocy.EditObject = {!! json_encode($barcode) !!};
		</script>
		@endif

		<form id="barcode-form"
			novalidate>

			<input type="hidden"
				name="product_id"
				value="{{ $product->id }}">

			<div class="form-group">
				<label for="name">{{ $__t('Barcode') }}&nbsp;<i class="fa-solid fa-barcode"></i></label>
				<div class="input-group">
					<input type="text"
						class="form-control barcodescanner-input"
						required
						id="barcode"
						name="barcode"
						value="@if($mode == 'edit'){{ $barcode->barcode }}@endif"
						data-target="#barcode">
					@include('components.camerabarcodescanner')
				</div>
			</div>

			@php if($mode == 'edit') { $value = $barcode->amount; } else { $value = ''; } @endphp
			@include('components.productamountpicker', array(
			'value' => $value,
			'isRequired' => false
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			<div class="form-group">
				<label for="shopping_location_id_id">{{ $__t('Store') }}</label>
				<select class="custom-control custom-select"
					id="shopping_location_id"
					name="shopping_location_id">
					<option></option>
					@foreach($shoppinglocations as $store)
					<option @if($mode=='edit'
						&&
						$store->id == $barcode->shopping_location_id) selected="selected" @endif value="{{ $store->id }}">{{ $store->name }}</option>
					@endforeach
				</select>
			</div>
			@else
			<input type="hidden"
				name="shopping_location_id"
				id="shopping_location_id"
				value="1">
			@endif

			<div class="form-group">
				<label for="note">{{ $__t('Note') }}</label>
				<input type="text"
					class="form-control"
					id="note"
					name="note"
					value="@if($mode == 'edit'){{ $barcode->note }}@endif">
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'product_barcodes'
			))

			<button id="save-barcode-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
