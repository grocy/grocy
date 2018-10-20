<!DOCTYPE html>
<html lang="{{ GROCY_CULTURE }}">
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
	<link href="{{ $U('/node_modules/@fortawesome/fontawesome-free/css/all.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/css/bootstrap-combobox.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-colreorder-bs4/css/colReorder.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/toastr/build/toastr.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/tagmanager/tagmanager.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/components_unmanaged/noto-sans-v6-latin/noto-sans-v6-latin.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/css/grocy.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/css/grocy_night_mode.css?v=', true) }}{{ $version }}" rel="stylesheet">
	@stack('pageStyles')

	@if(file_exists(GROCY_DATAPATH . '/custom_css.html'))
		@php include GROCY_DATAPATH . '/custom_css.html' @endphp
	@endif

	<script>
		var Grocy = { };
		Grocy.Components = { };
		Grocy.Mode = '{{ GROCY_MODE }}';
		Grocy.BaseUrl = '{{ $U('/') }}';
		Grocy.LocalizationStrings = {!! json_encode($localizationStrings) !!};
		Grocy.ActiveNav = '@yield('activeNav', '')';
		Grocy.Culture = '{{ GROCY_CULTURE }}';
		Grocy.Currency = '{{ GROCY_CURRENCY }}';
		Grocy.UserSettings = {!! json_encode($userSettings) !!};
	</script>
</head>

<body class="fixed-nav @if(boolval($userSettings['night_mode_enabled']) || (boolval($userSettings['auto_night_mode_enabled']) && boolval($userSettings['currently_inside_night_mode_range']))) night-mode @endif">
	<nav id="mainNav" class="navbar navbar-expand-lg navbar-light fixed-top">
		<a class="navbar-brand py-0" href="{{ $U('/') }}"><img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}" height="30"></a>
		
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#sidebarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div id="sidebarResponsive" class="collapse navbar-collapse">
			<ul class="navbar-nav navbar-sidenav pt-2">

				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Stock overview') }}" data-nav-for-page="stockoverview">
					<a class="nav-link discrete-link" href="{{ $U('/stockoverview') }}">
						<i class="fas fa-box"></i>
						<span class="nav-link-text">{{ $L('Stock overview') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Shopping list') }}" data-nav-for-page="shoppinglist">
					<a class="nav-link discrete-link" href="{{ $U('/shoppinglist') }}">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $L('Shopping list') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Recipes') }}" data-nav-for-page="recipes">
					<a class="nav-link discrete-link" href="{{ $U('/recipes') }}">
						<i class="fas fa-cocktail"></i>
						<span class="nav-link-text">{{ $L('Recipes') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Chores overview') }}" data-nav-for-page="choresoverview">
					<a class="nav-link discrete-link" href="{{ $U('/choresoverview') }}">
						<i class="fas fa-home"></i>
						<span class="nav-link-text">{{ $L('Chores overview') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Tasks') }}" data-nav-for-page="tasks">
					<a class="nav-link discrete-link" href="{{ $U('/tasks') }}">
						<i class="fas fa-tasks"></i>
						<span class="nav-link-text">{{ $L('Tasks') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Batteries overview') }}" data-nav-for-page="batteriesoverview">
					<a class="nav-link discrete-link" href="{{ $U('/batteriesoverview') }}">
						<i class="fas fa-battery-half"></i>
						<span class="nav-link-text">{{ $L('Batteries overview') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Equipment') }}" data-nav-for-page="equipment">
					<a class="nav-link discrete-link" href="{{ $U('/equipment') }}">
						<i class="fas fa-toolbox"></i>
						<span class="nav-link-text">{{ $L('Equipment') }}</span>
					</a>
				</li>
				
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="{{ $L('Purchase') }}" data-nav-for-page="purchase">
					<a class="nav-link discrete-link" href="{{ $U('/purchase') }}">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $L('Purchase') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Consume') }}" data-nav-for-page="consume">
					<a class="nav-link discrete-link" href="{{ $U('/consume') }}">
						<i class="fas fa-utensils"></i>
						<span class="nav-link-text">{{ $L('Consume') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Inventory') }}" data-nav-for-page="inventory">
					<a class="nav-link discrete-link" href="{{ $U('/inventory') }}">
						<i class="fas fa-list"></i>
						<span class="nav-link-text">{{ $L('Inventory') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Chore tracking') }}" data-nav-for-page="choretracking">
					<a class="nav-link discrete-link" href="{{ $U('/choretracking') }}">
						<i class="fas fa-play"></i>
						<span class="nav-link-text">{{ $L('Chore tracking') }}</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="{{ $L('Battery tracking') }}" data-nav-for-page="batterytracking">
					<a class="nav-link discrete-link" href="{{ $U('/batterytracking') }}">
						<i class="fas fa-fire"></i>
						<span class="nav-link-text">{{ $L('Battery tracking') }}</span>
					</a>
				</li>
				
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="{{ $L('Manage master data') }}">
					<a class="nav-link nav-link-collapse collapsed discrete-link" data-toggle="collapse" href="#top-nav-manager-master-data">
						<i class="fas fa-table"></i>
						<span class="nav-link-text">{{ $L('Manage master data') }}</span>
					</a>
					<ul id="top-nav-manager-master-data" class="sidenav-second-level collapse">
						<li data-nav-for-page="products" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/products') }}">
								<i class="fab fa-product-hunt"></i>
								<span class="nav-link-text">{{ $L('Products') }}</span>
							</a>
						</li>
						<li data-nav-for-page="locations" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/locations') }}">
								<i class="fas fa-map-marker-alt"></i>
								<span class="nav-link-text">{{ $L('Locations') }}</span>
							</a>
						</li>
						<li data-nav-for-page="quantityunits" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/quantityunits') }}">
								<i class="fas fa-balance-scale"></i>
								<span class="nav-link-text">{{ $L('Quantity units') }}</span>
							</a>
						</li>
						<li data-nav-for-page="productgroups" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/productgroups') }}">
								<i class="fas fa-object-group"></i>
								<span class="nav-link-text">{{ $L('Product groups') }}</span>
							</a>
						</li>
						<li data-nav-for-page="chores" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/chores') }}">
								<i class="fas fa-home"></i>
								<span class="nav-link-text">{{ $L('Chores') }}</span>
							</a>
						</li>
						<li data-nav-for-page="batteries" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/batteries') }}">
								<i class="fas fa-battery-half"></i>
								<span class="nav-link-text">{{ $L('Batteries') }}</span>
							</a>
						</li>
						<li data-nav-for-page="taskcategories" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/taskcategories') }}">
								<i class="fas fa-project-diagram "></i>
								<span class="nav-link-text">{{ $L('Task categories') }}</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>

			<ul class="navbar-nav sidenav-toggler">
				<li class="nav-item">
					<a id="sidenavToggler" class="nav-link text-center">
						<i class="fas fa-angle-left"></i>
					</a>
				</li>
			</ul>

			<ul class="navbar-nav ml-auto">
				@if(GROCY_AUTHENTICATED === true && !GROCY_IS_EMBEDDED_INSTALL)
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-user"></i> {{ GROCY_USER_USERNAME }}</a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item logout-button discrete-link" href="{{ $U('/logout') }}"><i class="fas fa-sign-out-alt"></i>&nbsp;{{ $L('Logout') }}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item logout-button discrete-link" href="{{ $U('/user/' . GROCY_USER_ID . '?changepw=true') }}"><i class="fas fa-key"></i>&nbsp;{{ $L('Change password') }}</a>
					</div>
				</li>
				@endif

				@if(GROCY_AUTHENTICATED === true)
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-sliders-h"></i> <span class="d-inline d-lg-none">{{ $L('View settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-reload-enabled" data-setting-key="auto_reload_on_db_change">
								<label class="form-check-label" for="auto-reload-enabled">
									{{ $L('Auto reload on external changes') }}
								</label>
							</div>
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="night-mode-enabled" data-setting-key="night_mode_enabled">
								<label class="form-check-label" for="night-mode-enabled">
									{{ $L('Enable night mode') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-enabled" data-setting-key="auto_night_mode_enabled">
								<label class="form-check-label" for="auto-night-mode-enabled">
									{{ $L('Auto enable in time range') }}
								</label>
							</div>
							<div class="form-inline">
								<input type="text" class="form-control my-1 user-setting-control" readonly id="auto-night-mode-time-range-from" placeholder="{{ $L('From') }} ({{ $L('in format') }} HH:mm)" data-setting-key="auto_night_mode_time_range_from">
								<input type="text" class="form-control user-setting-control" readonly id="auto-night-mode-time-range-to" placeholder="{{ $L('To') }} ({{ $L('in format') }} HH:mm)" data-setting-key="auto_night_mode_time_range_to">
							</div>
							<div class="form-check mt-1">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-time-range-goes-over-midgnight" data-setting-key="auto_night_mode_time_range_goes_over_midnight">
								<label class="form-check-label" for="auto-night-mode-time-range-goes-over-midgnight">
									{{ $L('Time range goes over midnight') }}
								</label>
							</div>
							<input class="form-check-input d-none user-setting-control" type="checkbox" id="currently-inside-night-mode-range" data-setting-key="currently_inside_night_mode_range">
						</div>
					</div>
				</li>
				@endif

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-wrench"></i> <span class="d-inline d-lg-none">{{ $L('Settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item discrete-link" href="{{ $U('/users') }}"><i class="fas fa-users"></i>&nbsp;{{ $L('Manage users') }}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="{{ $U('/manageapikeys') }}"><i class="fas fa-handshake"></i>&nbsp;{{ $L('Manage API keys') }}</a>
						<a class="dropdown-item discrete-link" target="_blank" href="{{ $U('/api') }}"><i class="fas fa-book"></i>&nbsp;{{ $L('REST API & data model documentation') }}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="#" data-toggle="modal" data-target="#about-modal"><i class="fas fa-info fa-fw"></i>&nbsp;{{ $L('About grocy') }} (Version {{ $version }})</a>
					</div>
				</li>
			</ul>
		</div>
	</nav>

	<div class="content-wrapper">
		<div class="container-fluid">
			<div class="row mb-3">
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
						<i class="fab fa-github"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ $U('/node_modules/jquery/dist/jquery.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/startbootstrap-sb-admin/js/sb-admin.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootbox/dist/bootbox.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/jquery-serializejson/jquery.serializejson.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/moment/min/moment.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($L('moment_locale')))<script src="{{ $U('/node_modules', true) }}/moment/locale/{{ $L('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net/js/jquery.dataTables.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive/js/dataTables.responsive.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder/js/dataTables.colReorder.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder-bs4/js/colReorder.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select/js/dataTables.select.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/timeago/jquery.timeago.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules', true) }}/timeago/locales/jquery.timeago.{{ $L('timeago_locale') }}.js?v={{ $version }}"></script>
	<script src="{{ $U('/node_modules/toastr/build/toastr.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tagmanager/tagmanager.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js?v=', true) }}{{ $version }}"></script>

	<script src="{{ $U('/js/extensions.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_dbchangedhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_nightmode.js?v=', true) }}{{ $version }}"></script>
	@stack('pageScripts')
	@stack('componentScripts')
	<script src="{{ $U('/viewjs', true) }}/@yield('viewJsName').js?v={{ $version }}"></script>

	@if(file_exists(GROCY_DATAPATH . '/custom_js.html'))
		@php include GROCY_DATAPATH . '/custom_js.html' @endphp
	@endif
</body>

</html>
