@if (!GROCY_FEATURE_FLAG_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/barcodescanner.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@once
@push('pageScripts')
<script src="{{ $U('/node_modules/@ericblade/quagga2/dist/quagga.min.js?v=', true) }}{{ $version }}"></script>
@endpush
@endonce

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
