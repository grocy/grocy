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

	.input-group-prepend #barcodescanner-start-button {
		position: static;
		right: unset;
		margin: unset;
	}

	.input-group>#barcodescanner-start-button-container+.select2-hidden-accessible+.select2-container--bootstrap>.selection>.select2-selection, .input-group>#barcodescanner-start-button-container+.select2-hidden-accessible+.select2-container--bootstrap>.selection>.select2-selection.form-control {
		border-top-right-radius: .25rem;
		border-bottom-right-radius: .25rem;
	}

</style>
@endpush

@endif
