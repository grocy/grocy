<html>

<head>
	<title>{{ $product->name }}</title>
	<link href="{{ $U('/packages/@fontsource/roboto/latin.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<style>
		body {
			font-family: 'Roboto', sans-serif;
		}

		img {
			float: left;
			margin-right: .5rem;
			max-height: 25px;
			width: auto;
			margin-top: 2px;
		}

		.productname {
			font-size: 20px;
			display: inline-block;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<p>
		<!-- Size gets determined by CSS, so printing works better (more pixels = sharper printed image).
	         Unfortunately, this also means the code is blurred on screen. -->
		<img src="{{ $U('/stockentry/'. $stockEntry->id . '/grocycode?size=100') }}">
		<span class="productname">{{ $product->name }}</span><br>
		@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		<span>{{ $__t('DD') }}: {{ $stockEntry->best_before_date }}</span>
		@endif
	</p>
</body>

</html>
