@if (!GROCY_FEATURE_FLAG_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)

@php require_frontend_packages(['quagga2']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/camerabarcodescanner.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@push('pageStyles')
<style>
	#camerabarcodescanner-start-button {
		position: absolute;
		right: 0;
		margin-top: 4px;
		margin-right: 5px;
		cursor: pointer;
	}

	.combobox-container #camerabarcodescanner-start-button {
		margin-right: 38px !important;
	}
</style>
@endpush

@endif
