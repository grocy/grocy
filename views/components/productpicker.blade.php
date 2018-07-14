@push('componentScripts')
	<script src="{{ $U('/viewjs/components/productpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($disallowAddProductWorkflows)) { $disallowAddProductWorkflows = false; } @endphp
@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp

<div class="form-group" data-next-input-selector="{{ $nextInputSelector }}" data-disallow-add-product-workflows="{{ BoolToString($disallowAddProductWorkflows) }}" data-prefill-by-name="{{ $prefillByName }}">
	<label for="product_id">{{ $L('Product') }} <i class="fas fa-barcode"></i><span id="barcode-lookup-disabled-hint" class="small text-muted d-none"> Barcode lookup is disabled</span></label>
	<select class="form-control combobox" id="product_id" name="product_id" required>
		<option value=""></option>
		@foreach($products as $product)
			<option data-additional-searchdata="{{ $product->barcode }}" value="{{ $product->id }}">{{ $product->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $L('You have to select a product') }}</div>
	<div id="custom-productpicker-error" class="form-text text-danger d-none"></div>
	<div id="flow-info-addbarcodetoselection" class="form-text text-muted small d-none"><strong><span id="addbarcodetoselection"></span></strong> {{ $L('will be added to the list of barcodes for the selected product on submit') }}</div>
</div>
