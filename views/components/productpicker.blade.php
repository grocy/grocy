@once
    @push('componentScripts')
        <script src="{{ $U('/viewjs/components/productpicker.js', true) }}?v={{ $version }}"></script>
    @endpush
@endonce

@php
if (empty($disallowAddProductWorkflows)) {
    $disallowAddProductWorkflows = false;
}
if (empty($disallowAllProductWorkflows)) {
    $disallowAllProductWorkflows = false;
}
if (empty($prefillByName)) {
    $prefillByName = '';
}
if (empty($prefillById)) {
    $prefillById = '';
}
if (!isset($isRequired)) {
    $isRequired = true;
}
if (!isset($label)) {
    $label = 'Product';
}
if (!isset($disabled)) {
    $disabled = false;
}
if (empty($hint)) {
    $hint = '';
}
if (empty($nextInputSelector)) {
    $nextInputSelector = '';
}
if (empty($validationMessage)) {
    $validationMessage = 'You have to select a product';
}
if (empty($productsQuery)) {
    $productsQuery = '';
}
@endphp

<div class="form-group" data-next-input-selector="{{ $nextInputSelector }}"
    data-disallow-add-product-workflows="{{ BoolToString($disallowAddProductWorkflows) }}"
    data-disallow-all-product-workflows="{{ BoolToString($disallowAllProductWorkflows) }}"
    data-prefill-by-name="{{ $prefillByName }}" data-prefill-by-id="{{ $prefillById }}"
    data-products-query="{{ $productsQuery }}">
    <label class="w-100" for="product_id">
        {{ $__t($label) }}
        @if (!$disallowAllProductWorkflows)
            <i class="fas fa-question-circle text-muted" data-toggle="tooltip" data-trigger="hover click"
                title="{{ $__t('Type a new product name or barcode and hit TAB or ENTER to start a workflow') }}"></i>
        @endif
        @if (!empty($hint))
            <i class="fas fa-question-circle text-muted" data-toggle="tooltip" data-trigger="hover click"
                title="{{ $hint }}"></i>
        @endif
        <span id="barcode-lookup-disabled-hint" class="small text-muted d-none float-right">
            {{ $__t('Barcode lookup is disabled') }}</span>
        <i id="barcode-lookup-hint" class="fas fa-barcode float-right mt-1"></i>
    </label>
    <div class="input-group">
        <select class="select2 custom-control custom-select barcodescanner-input" id="product_id" name="product_id"
            @if ($isRequired) required @endif @if ($disabled) disabled @endif
            data-target="@productpicker"></select>
        <div class="invalid-feedback">{{ $__t($validationMessage) }}</div>
    </div>
    <div id="custom-productpicker-error" class="form-text text-danger d-none"></div>
    <div id="flow-info-InplaceAddBarcodeToExistingProduct" class="form-text text-info small d-none"><strong><span
                id="InplaceAddBarcodeToExistingProduct"></span></strong>
        {{ $__t('will be added to the list of barcodes for the selected product on submit') }}</div>
</div>

@include('components.barcodescanner')
