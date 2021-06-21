@if (!GROCY_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)

@push('pageStyles')
<style>
	#barcodescanner-start-button {
		position: absolute;
		right: 0;
		margin-top: 4px;
		margin-right: 5px;
		cursor: pointer;
	}

	.combobox-container #barcodescanner-start-button {
		margin-right: 36px !important;
	}
</style>
@endpush

@endif