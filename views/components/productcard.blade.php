<div class="card productcard">
	<div class="card-header">
		<span class="float-left">{{ $__t('Product overview') }}</span>
		<a id="productcard-product-edit-button"
			class="btn btn-sm btn-outline-secondary py-0 float-right disabled"
			href="#"
			data-toggle="tooltip"
			title="{{ $__t('Edit product') }}">
			<i class="fas fa-edit"></i>
		</a>
		@if(!isset($noStockJournalButton))
		<a id="productcard-product-journal-button"
			class="btn btn-sm btn-outline-secondary py-0 mr-1 float-right disabled show-as-dialog-link"
			href="#">
			{{ $__t('Stock journal') }}
		</a>
		@endif
		@if(!isset($noStockEntryButton))
		<a id="productcard-product-stock-button"
			class="btn btn-sm btn-outline-secondary py-0 mr-1 float-right disabled show-as-dialog-link"
			href="#">
			{{ $__t('Stock entries') }}
		</a>
		@endif
	</div>
	<div class="card-body">
		<h3><span id="productcard-product-name"></span></h3>

		<div id="productcard-product-description-wrapper"
			class="expandable-text mb-2 d-none">
			<p id="productcard-product-description"
				class="text-muted collapse mb-0"></p>
			<a class="collapsed"
				data-toggle="collapse"
				href="#productcard-product-description">{{ $__t('Show more') }}</a>
		</div>

		<strong>{{ $__t('Stock amount') }}:</strong> <span id="productcard-product-stock-amount"
			class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name"></span>
		<span id="productcard-product-stock-opened-amount"
			class="small font-italic locale-number locale-number-quantity-amount"></span>
		<span id="productcard-aggregated-amounts"
			class="pl-2 text-secondary d-none"><i class="fas fa-custom-sigma-sign"></i> <span id="productcard-product-stock-amount-aggregated"
				class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name-aggregated"></span> <span id="productcard-product-stock-opened-amount-aggregated locale-number locale-number-quantity-amount"
				class="small font-italic"></span></span><br>
		<strong>{{ $__t('Stock value') }}:</strong> <span id="productcard-product-stock-value"
			class="locale-number locale-number-currency"></span><br>
		@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)<strong>{{ $__t('Default location') }}:</strong> <span id="productcard-product-location"></span><br>@endif
		<strong>{{ $__t('Last purchased') }}:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago"
			class="timeago timeago-contextual"></time><br>
		<strong>{{ $__t('Last used') }}:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago"
			class="timeago timeago-contextual"></time><br>
		@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)<strong>{{ $__t('Last price') }}:</strong> <span id="productcard-product-last-price"></span><br>@endif
		@if (GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)<strong>{{ $__t('Average price') }}:</strong> <span id="productcard-product-average-price"></span><br>@endif
		@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)<strong>{{ $__t('Average shelf life') }}:</strong> <span id="productcard-product-average-shelf-life"></span><br>@endif
		<strong>{{ $__t('Spoil rate') }}:</strong> <span id="productcard-product-spoil-rate"></span>

		<p class="w-75 mt-3 mx-auto"><img id="productcard-product-picture"
				data-src=""
				class="img-fluid img-thumbnail d-none lazy"></p>

		@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
		<h5 class="mt-3">{{ $__t('Price history') }}</h5>
		<canvas id="productcard-product-price-history-chart"
			class="w-100 d-none"></canvas>
		<span id="productcard-no-price-data-hint"
			class="font-italic d-none">{{ $__t('No price history available') }}</span>
		@endif
	</div>
</div>
