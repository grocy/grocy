@push('componentScripts')
	<script src="{{ $U('/node_modules/chart.js/dist/Chart.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/viewjs/components/productcard.js', true) }}?v={{ $version }}"></script>
@endpush

<div class="card">
	<div class="card-header">
		<i class="fab fa-product-hunt"></i> {{ $L('Product overview') }}
		<a id="productcard-product-edit-button" class="btn btn-sm btn-outline-info py-0 float-right disabled" href="#" data-toggle="tooltip" title="{{ $L('Edit product') }}">
			<i class="fas fa-edit"></i>
		</a>
	</div>
	<div class="card-body">
		<h3><span id="productcard-product-name"></span></h3>

		<div id="productcard-product-description-wrapper" class="expandable-text mb-2 d-none">
			<p id="productcard-product-description" class="text-muted collapse mb-0"></p>
			<a class="collapsed" data-toggle="collapse" href="#productcard-product-description">{{ $L('Show more') }}</a>
		</div>

		<strong>{{ $L('Stock amount') . ' / ' . $L('Quantity unit') }}:</strong> <span id="productcard-product-stock-amount"></span> <span id="productcard-product-stock-qu-name"></span> <span id="productcard-product-stock-opened-amount" class="small font-italic"></span><br>
		<strong>{{ $L('Location') }}:</strong> <span id="productcard-product-location"></span><br>
		<strong>{{ $L('Last purchased') }}:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong>{{ $L('Last used') }}:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago" class="timeago timeago-contextual"></time><br>
		<strong>{{ $L('Last price') }}:</strong> <span id="productcard-product-last-price"></span><br>
		<strong>{{ $L('Average shelf life') }}:</strong> <span id="productcard-product-average-shelf-life"></span><br>
		<strong>{{ $L('Spoil rate') }}:</strong> <span id="productcard-product-spoil-rate"></span>

		<h5 class="mt-3">{{ $L('Product picture') }}</h5>
		<p class="w-75 mx-auto"><img id="productcard-product-picture" src="" class="img-fluid img-thumbnail d-none"></p>
		<span id="productcard-no-product-picture" class="font-italic d-none">{{ $L('No picture available') }}</span>

		<h5 class="mt-3">{{ $L('Price history') }}</h5>
		<canvas id="productcard-product-price-history-chart" class="w-100 d-none"></canvas>
		<span id="productcard-no-price-data-hint" class="font-italic d-none">{{ $L('No price history available') }}</span>
	</div>
</div>
