@push('componentScripts')
	<script src="{{ $U('/viewjs/components/productpicker.js', true) }}?v={{ $version }}"></script>
@endpush

@php if(empty($disallowAddProductWorkflows)) { $disallowAddProductWorkflows = false; } @endphp
@php if(empty($disallowAllProductWorkflows)) { $disallowAllProductWorkflows = false; } @endphp
@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($label)) { $label = 'Product'; } @endphp
@php if(!isset($disabled)) { $disabled = false; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group" data-next-input-selector="{{ $nextInputSelector }}" data-disallow-add-product-workflows="{{ BoolToString($disallowAddProductWorkflows) }}" data-disallow-all-product-workflows="{{ BoolToString($disallowAllProductWorkflows) }}" data-prefill-by-name="{{ $prefillByName }}" data-prefill-by-id="{{ $prefillById }}">
	<label for="product_id">
		{{ $__t($label) }}&nbsp;<i class="fas fa-barcode"></i>&nbsp;
		<span id="barcode-lookup-disabled-hint" class="small text-muted d-none"> {{ $__t('Barcode lookup is disabled') }}</span>&nbsp;
		<i class="fas fa-question-circle" data-toggle="tooltip" title="{{ $hint }}"></i>
	</label>
	<select class="form-control product-combobox barcodescanner-input" id="product_id" name="product_id" @if($isRequired) required @endif @if($disabled) disabled @endif data-target="@productpicker">
		<option value=""></option>
		@foreach($products as $product)
			<option data-additional-searchdata="{{ $product->barcode }}@if(!empty($product->barcode)),@endif" value="{{ $product->id }}">{{ $product->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t('You have to select a product') }}</div>
	<div id="custom-productpicker-error" class="form-text text-danger d-none"></div>
	@if(!$disallowAllProductWorkflows)
		<div class="form-text text-info small">{{ $__t('Type a new product name or barcode and hit TAB to start a workflow') }}</div>
	@endif
	<div id="flow-info-addbarcodetoselection" class="form-text text-muted small d-none"><strong><span id="addbarcodetoselection"></span></strong> {{ $__t('will be added to the list of barcodes for the selected product on submit') }}</div>
</div>

@include('components.barcodescanner')
