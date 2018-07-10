<!DOCTYPE html>
<html lang="{{ CULTURE }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">
	<link rel="icon" href="{{ $U('/img/grocy_icon.svg?v=', true) }}{{ $version }}">

	<title>@yield('title') | grocy</title>

	<link href="{{ $U('/node_modules/bootstrap/dist/css/bootstrap.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/startbootstrap-sb-admin/css/sb-admin.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/font-awesome/css/font-awesome.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/css/bootstrap-combobox.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/toastr/build/toastr.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/tagmanager/tagmanager.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/components_unmanaged/noto-sans-v6-latin/noto-sans-v6-latin.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/css/grocy.css?v=', true) }}{{ $version }}" rel="stylesheet">
	@stack('pageStyles')

	<script>
		var Grocy = { };
		Grocy.Components = { };
		Grocy.BaseUrl = '{{ $U('/') }}';
		Grocy.LocalizationStrings = {!! json_encode($localizationStrings) !!};
		Grocy.ActiveNav = '@yield('activeNav', '')';
	</script>
</head>

<body class="fixed-nav">
	<nav id="mainNav" class="navbar navbar-expand-lg navbar-light fixed-top">
		<a class="navbar-brand" href="{{ $U('/') }}"><img src="{{ $U('/img/grocy_logo.svg?v=', true) }}" height="30"></a>
		
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#sidebarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div id="sidebarResponsive" class="collapse navbar-collapse">
			<ul class="navbar-nav navbar-sidenav">

				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Stock overview') }}" data-nav-for-page="stockoverview">
					<a class="nav-link discrete-link" href="{{ $U('/stockoverview') }}">
						<i class="fa fa-fw fa-dashboard"></i>
						<span class="nav-link-text">{{ $L('Stock overview') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Habits overview') }}" data-nav-for-page="habitsoverview">
					<a class="nav-link discrete-link" href="{{ $U('/habitsoverview') }}">
						<i class="fa fa-fw fa-dashboard"></i>
						<span class="nav-link-text">{{ $L('Habits overview') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Batteries overview') }}" data-nav-for-page="batteriesoverview">
					<a class="nav-link discrete-link" href="{{ $U('/batteriesoverview') }}">
						<i class="fa fa-fw fa-dashboard"></i>
						<span class="nav-link-text">{{ $L('Batteries overview') }}</span>
					</a>
				</li>

				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="{{ $L('Purchase') }}" data-nav-for-page="purchase">
					<a class="nav-link discrete-link" href="{{ $U('/purchase') }}">
						<i class="fa fa-fw fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $L('Purchase') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Consume') }}" data-nav-for-page="consume">
					<a class="nav-link discrete-link" href="{{ $U('/consume') }}">
						<i class="fa fa-fw fa-cutlery"></i>
						<span class="nav-link-text">{{ $L('Consume') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Shopping list') }}" data-nav-for-page="shoppinglist">
					<a class="nav-link discrete-link" href="{{ $U('/shoppinglist') }}">
						<i class="fa fa-fw fa-shopping-bag"></i>
						<span class="nav-link-text">{{ $L('Shopping list') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Inventory') }}" data-nav-for-page="inventory">
					<a class="nav-link discrete-link" href="{{ $U('/inventory') }}">
						<i class="fa fa-fw fa-list"></i>
						<span class="nav-link-text">{{ $L('Inventory') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Habit tracking') }}" data-nav-for-page="habittracking">
					<a class="nav-link discrete-link" href="{{ $U('/habittracking') }}">
						<i class="fa fa-fw fa-play"></i>
						<span class="nav-link-text">{{ $L('Habit tracking') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Battery tracking') }}" data-nav-for-page="batterytracking">
					<a class="nav-link discrete-link" href="{{ $U('/batterytracking') }}">
						<i class="fa fa-fw fa-fire"></i>
						<span class="nav-link-text">{{ $L('Battery tracking') }}</span>
					</a>
				</li>
				
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="{{ $L('Manage master data') }}">
					<a class="nav-link nav-link-collapse collapsed discrete-link" data-toggle="collapse" href="#top-nav-manager-master-data">
						<i class="fa fa-fw fa-wrench"></i>
						<span class="nav-link-text">{{ $L('Manage master data') }}</span>
					</a>
					<ul id="top-nav-manager-master-data" class="sidenav-second-level collapse">
						<li data-nav-for-page="products">
							<a class="nav-link discrete-link" href="{{ $U('/products') }}">
								<i class="fa fa-fw fa-product-hunt"></i>
								<span class="nav-link-text">{{ $L('Products') }}</span>
							</a>
						</li>
						<li data-nav-for-page="locations">
							<a class="nav-link discrete-link" href="{{ $U('/locations') }}">
								<i class="fa fa-fw fa-map-marker"></i>
								<span class="nav-link-text">{{ $L('Locations') }}</span>
							</a>
						</li>
						<li data-nav-for-page="quantityunits">
							<a class="nav-link discrete-link" href="{{ $U('/quantityunits') }}">
								<i class="fa fa-fw fa-balance-scale"></i>
								<span class="nav-link-text">{{ $L('Quantity units') }}</span>
							</a>
						</li>
						<li data-nav-for-page="habits">
							<a class="nav-link discrete-link" href="{{ $U('/habits') }}">
								<i class="fa fa-fw fa-refresh"></i>
								<span class="nav-link-text">{{ $L('Habits') }}</span>
							</a>
						</li>
						<li data-nav-for-page="batteries">
							<a class="nav-link discrete-link" href="{{ $U('/batteries') }}">
								<i class="fa fa-fw fa-battery-three-quarters"></i>
								<span class="nav-link-text">{{ $L('Batteries') }}</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>

			<ul class="navbar-nav sidenav-toggler">
				<li class="nav-item">
					<a id="sidenavToggler" class="nav-link text-center">
						<i class="fa fa-fw fa-angle-left"></i>
					</a>
				</li>
			</ul>

			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle mr-lg-2" href="#" data-toggle="dropdown">@if(AUTHENTICATED === true){{ HTTP_USER }}@else{{ $L('Not logged in') }}@endif</a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item logout-button discrete-link" href="{{ $U('/logout') }}"><i class="fa fa-sign-out fa-fw"></i>&nbsp;{{ $L('Logout') }}</a>
						
						<div class="dropdown-divider logout-button-divider"></div>

						<a class="dropdown-item discrete-link" href="{{ $U('/manageapikeys') }}"><i class="fa fa-handshake-o fa-fw"></i>&nbsp;{{ $L('Manage API keys') }}</a>
						<a class="dropdown-item discrete-link" target="_blank" href="{{ $U('/api') }}"><i class="fa fa-book"></i>&nbsp;{{ $L('REST API & data model documentation') }}</a>
						
						<div class="dropdown-divider"></div>

						<a class="dropdown-item discrete-link" href="#" data-toggle="modal" data-target="#about-modal"><i class="fa fa-info fa-fw"></i>&nbsp;{{ $L('About grocy') }} (Version {{ $version }})</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>

	<div class="content-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col content-text">
					@yield('content')
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade content-text" id="about-modal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content text-center">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{ $L('About grocy') }}</h4>
					<button type="button" class="close" data-dismiss="modal" title="{{ $L('Close') }}">&times;</button>
				</div>
				<div class="modal-body">
					grocy is a project by
					<a href="https://berrnd.de" class="discrete-link" target="_blank">Bernd Bestel</a><br>
					Created with passion since 2017<br>
					<br>
					Version {{ $version }}<br>
					{{ $L('Released on') }} {{ $releaseDate }} <time class="timeago timeago-contextual" datetime="{{ $releaseDate }}"></time><br>
					<br>
					Life runs on code<br>
					<a href="https://github.com/berrnd/grocy" class="discrete-link" target="_blank">
						<i class="fa fa-github"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ $U('/node_modules/jquery/dist/jquery.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/startbootstrap-sb-admin/js/sb-admin.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootbox/dist/bootbox.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/jquery-serializeJSON/jquery.serializejson.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($L('bootstrap_datepicker_locale')))<script src="{{ $U('/bower_components', true) }}/bootstrap-datepicker/dist/locales/bootstrap-datepicker.{{ $L('bootstrap_datepicker_locale') }}.min.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/moment/min/moment.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($L('moment_locale')))<script src="{{ $U('/bower_components', true) }}/moment/locale/{{ $L('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/bootstrap-validator/dist/validator.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net/js/jquery.dataTables.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive/js/dataTables.responsive.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/timeago/jquery.timeago.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules', true) }}/timeago/locales/jquery.timeago.{{ $L('timeago_locale') }}.js?v={{ $version }}"></script>
	<script src="{{ $U('/node_modules/toastr/build/toastr.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tagmanager/tagmanager.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js?v=', true) }}{{ $version }}"></script>

	<script src="{{ $U('/js/extensions.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=', true) }}{{ $version }}"></script>
	@stack('pageScripts')
	@stack('componentScripts')
	<script src="{{ $U('/viewjs', true) }}/@yield('viewJsName').js?v={{ $version }}"></script>

	@if(file_exists(__DIR__ . '/../../data/add_before_end_body.html'))
		@php include __DIR__ . '/../../data/add_before_end_body.html' @endphp
	@endif
</body>

</html>
