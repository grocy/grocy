@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit Barcode'))
@else
@section('title', $__t('Create Barcode'))
@endif

@section('viewJsName', 'productbarcodeform')

@section('grocyConfigProps')
	QuantityUnits: {!! json_encode($quantityUnits) !!},
	QuantityUnitConversionsResolved: {!! json_encode($quantityUnitConversionsResolved) !!},
	EditMode: '{{ $mode }}',
@if($mode == 'edit')	
	EditObjectId: {{ $barcode->id }},
	EditObject: {!! json_encode($barcode) !!},
@endif
	EditObjectProduct: {!! json_encode($product) !!},
@endsection

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<h2>
				<span class="text-muted small">{{ $__t('Barcode for product') }} <strong>{{ $product->name }}</strong></span>
			</h2>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<form id="barcode-form"
			novalidate>

			<input type="hidden"
				name="product_id"
				value="{{ $product->id }}">

			<div class="form-group">
				<label for="name">{{ $__t('Barcode') }}&nbsp;<i class="fas fa-barcode"></i></label>
				<div class="input-group">
					<input type="text"
						class="form-control barcodescanner-input"
						required
						id="barcode"
						name="barcode"
						value="@if($mode == 'edit'){{ $barcode->barcode }}@endif"
						data-target="#barcode">
					@include('components.barcodescanner')
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
