@push('componentScripts')
	<script src="{{ $U('/viewjs/components/productcard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fab fa-product-hunt"></i> {{ $L('Product overview') }}
	</div>
	<div class="card-body">
		<h3><span id="productcard-product-name"></span></h3>
		<strong>{{ $L('Stock quantity unit') }}:</strong> <span id="productcard-product-stock-qu-name"></span><br>
		<strong>{{ $L('Stock amount') }}:</strong> <span id="productcard-product-stock-amount"></span> <span id="productcard-product-stock-qu-name2"></span><br>
		<strong>{{ $L('Last purchased') }}:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong>{{ $L('Last used') }}:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago" class="timeago timeago-contextual"></time>
	</div>
</div>
