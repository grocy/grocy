@push('componentScripts')
	<script src="/viewjs/components/productcard.js"></script>
@endpush

<div class="main well">

	<h3>Product overview <strong><span id="productcard-product-name"></span></strong></h3>
	<h4><strong>Stock quantity unit:</strong> <span id="productcard-product-stock-qu-name"></span></h4>

	<p>
		<strong>Stock amount:</strong> <span id="productcard-product-stock-amount"></span> <span id="productcard-product-stock-qu-name2"></span><br>
		<strong>Last purchased:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago" class="timeago timeago-contextual"></time><br>
		<strong>Last used:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago" class="timeago timeago-contextual"></time>
	</p>
	
</div>
