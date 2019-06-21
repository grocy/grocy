<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">
	<link rel="icon" href="{{ $U('/img/grocy_icon.svg?v=', true) }}{{ $version }}">

	<title>{{ $__t('REST API & data model documentation') }} | grocy</title>

	<link href="{{ $U('/node_modules/swagger-ui-dist/swagger-ui.css?v=', true) }}{{ $version }}" rel="stylesheet">

	@if(file_exists(GROCY_DATAPATH . '/custom_css.html'))
		@php include GROCY_DATAPATH . '/custom_css.html' @endphp
	@endif
	
	<script>
		var Grocy = { };
		Grocy.OpenApi = { };
		Grocy.OpenApi.SpecUrl = '{{ $U('/api/openapi/specification') }}';
	</script>
</head>

<body>
	<div id="swagger-ui"></div>

	<script src="{{ $U('/node_modules/swagger-ui-dist/swagger-ui.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/swagger-ui-dist/swagger-ui-bundle.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/swagger-ui-dist/swagger-ui-standalone-preset.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/viewjs', true) }}/openapiui.js?v={{ $version }}"></script>

	@if(file_exists(GROCY_DATAPATH . '/custom_js.html'))
		@php include GROCY_DATAPATH . '/custom_js.html' @endphp
	@endif
</body>
</html>
