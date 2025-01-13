@php require_frontend_packages(['bootstrap-combobox']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/productpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@push('componentScripts')
<script>
	Grocy.ExternalBarcodeLookupPluginName = "{{ $ExternalBarcodeLookupPluginName }}";
</script>
@endpush
@endonce

@php if(empty($disallowAddProductWorkflows)) { $disallowAddProductWorkflows = false; } @endphp
@php if(empty($disallowAllProductWorkflows)) { $disallowAllProductWorkflows = false; } @endphp
@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($label)) { $label = 'Product'; } @endphp
@php if(!isset($disabled)) { $disabled = false; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp
@php if(empty($validationMessage)) { $validationMessage = 'You have to select a product'; } @endphp
@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp

<div class="form-group {{ $additionalGroupCssClasses }}"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-disallow-add-product-workflows="{{ BoolToString($disallowAddProductWorkflows) }}"
	data-disallow-all-product-workflows="{{ BoolToString($disallowAllProductWorkflows) }}"
	data-prefill-by-name="{{ $prefillByName }}"
	data-prefill-by-id="{{ $prefillById }}">
	<label class="w-100"
		for="product_id">
		{{ $__t($label) }}
		@if(!$disallowAllProductWorkflows)
		<i class="fa-solid fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $__t('Type a new product name or barcode and hit TAB or ENTER to start a workflow') }}"></i>
		@endif
		@if(!empty($hint))
		<i class="fa-solid fa-question-circle text-muted"
			data-toggle="tooltip"
			data-trigger="hover click"
			title="{{ $hint }}"></i>
		@endif
		<span id="barcode-lookup-disabled-hint"
			class="small text-muted d-none float-right"> {{ $__t('Barcode lookup is disabled') }}</span>
		<i id="barcode-lookup-hint"
			class="fa-solid fa-barcode float-right mt-1"></i>
	</label>
	<select class="form-control product-combobox barcodescanner-input"
		id="product_id"
		name="product_id"
		@if($isRequired)
		required
		@endif
		@if($disabled)
		disabled
		@endif
		data-target="@productpicker">
		<option value=""></option>
		@foreach($products as $product)
		@php $bc = null;
		if(isset($barcodes)) {
		$bc = FindObjectInArrayByPropertyValue($barcodes, 'product_id', $product->id);
		}
		@endphp
		<option data-additional-searchdata="@if(isset($bc)){{ strtolower($bc->barcodes) }}@endif,"
			value="{{ $product->id }}">{{ $product->name }}</option>
		@endforeach
	</select>
	<div class="invalid-feedback">{{ $__t($validationMessage) }}</div>
	<div id="custom-productpicker-error"
		class="form-text text-danger d-none"></div>
	<div id="flow-info-InplaceAddBarcodeToExistingProduct"
		class="form-text text-info small d-none"><strong><span id="InplaceAddBarcodeToExistingProduct"></span></strong> {{ $__t('will be added to the list of barcodes for the selected product on submit') }}</div>
</div>

@include('components.camerabarcodescanner')
