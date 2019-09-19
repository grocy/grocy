@if (!GROCY_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)

@push('componentScripts')
	<script src="{{ $U('/viewjs/components/barcodescanner.js', true) }}?v={{ $version }}"></script>
@endpush

@push('pageScripts')
	<script src="{{ $U('/node_modules/quagga/dist/quagga.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@endif
