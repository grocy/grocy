<!DOCTYPE html>
<html lang="{{ GROCY_CULTURE }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">

	<link rel="apple-touch-icon" sizes="180x180" href="{{ $U('/img/appicons/apple-touch-icon.png?v=', true) }}{{ $version }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ $U('/img/appicons/favicon-32x32.png?v=', true) }}{{ $version }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ $U('/img/appicons/favicon-16x16.png?v=', true) }}{{ $version }}">
	<link rel="manifest" href="{{ $U('/img/appicons/site.webmanifest?v=', true) }}{{ $version }}">
	<link rel="mask-icon" href="{{ $U('/img/appicons/safari-pinned-tab.svg?v=', true) }}{{ $version }}" color="#0b024c">
	<link rel="shortcut icon" href="{{ $U('/img/appicons/favicon.ico?v=', true) }}{{ $version }}">
	<meta name="apple-mobile-web-app-title" content="grocy">
	<meta name="application-name" content="grocy">
	<meta name="msapplication-TileColor" content="#e5e5e5">
	<meta name="msapplication-config" content="{{ $U('/img/appicons/browserconfig.xml?v=', true) }}{{ $version }}">
	<meta name="theme-color" content="#ffffff">

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
	<link href="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/summernote/dist/summernote-bs4.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/components_unmanaged/noto-sans-v9-latin/noto-sans-v9-latin.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
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
		Grocy.CurrentUrlRelative = "/" + window.location.href.split('?')[0].replace(Grocy.BaseUrl, "");
		Grocy.ActiveNav = '@yield('activeNav', '')';
		Grocy.Culture = '{{ GROCY_CULTURE }}';
		Grocy.Currency = '{{ GROCY_CURRENCY }}';
		Grocy.CalendarFirstDayOfWeek = '{{ GROCY_CALENDAR_FIRST_DAY_OF_WEEK }}';
		Grocy.CalendarShowWeekNumbers = {{ BoolToString(GROCY_CALENDAR_SHOW_WEEK_OF_YEAR) }};
		Grocy.GettextPo = {!! $GettextPo !!};
		Grocy.FeatureFlags = {!! json_encode($featureFlags) !!};

		@if (GROCY_AUTHENTICATED)
		Grocy.UserSettings = {!! json_encode($userSettings) !!};
		Grocy.UserId = {{ GROCY_USER_ID }};
		@else
		Grocy.UserSettings = { };
		Grocy.UserId = -1;
		@endif
	</script>
</head>

<body class="fixed-nav @if(boolval($userSettings['night_mode_enabled']) || (boolval($userSettings['auto_night_mode_enabled']) && boolval($userSettings['currently_inside_night_mode_range']))) night-mode @endif @if($embedded) embedded @endif">
	@if(!($embedded))
	<nav id="mainNav" class="navbar navbar-expand-lg navbar-light fixed-top">
		<a class="navbar-brand py-0" href="{{ $U('/') }}"><img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}" height="30"></a>
		<span id="clock-container" class="text-muted font-italic d-none">
			<i class="far fa-clock"></i>
			<span id="clock-small" class="d-inline d-sm-none"></span>
			<span id="clock-big" class="d-none d-sm-inline"></span>
		</span>
		
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#sidebarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>

		@if(GROCY_AUTHENTICATED)
		<div id="sidebarResponsive" class="collapse navbar-collapse">
			<ul class="navbar-nav navbar-sidenav">

				@if(GROCY_FEATURE_FLAG_STOCK)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Stock overview') }}" data-nav-for-page="stockoverview">
					<a class="nav-link discrete-link" href="{{ $U('/stockoverview') }}">
						<i class="fas fa-box"></i>
						<span class="nav-link-text">{{ $__t('Stock overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Shopping list') }}" data-nav-for-page="shoppinglist">
					<a class="nav-link discrete-link" href="{{ $U('/shoppinglist') }}">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $__t('Shopping list') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_RECIPES)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Recipes') }}" data-nav-for-page="recipes">
					<a class="nav-link discrete-link" href="{{ $U('/recipes') }}">
						<i class="fas fa-cocktail"></i>
						<span class="nav-link-text">{{ $__t('Recipes') }}</span>
					</a>
				</li>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Meal plan') }}" data-nav-for-page="mealplan">
					<a class="nav-link discrete-link" href="{{ $U('/mealplan') }}">
						<i class="fas fa-paper-plane"></i>
						<span class="nav-link-text">{{ $__t('Meal plan') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_CHORES)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Chores overview') }}" data-nav-for-page="choresoverview">
					<a class="nav-link discrete-link" href="{{ $U('/choresoverview') }}">
						<i class="fas fa-home"></i>
						<span class="nav-link-text">{{ $__t('Chores overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_TASKS)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Tasks') }}" data-nav-for-page="tasks">
					<a class="nav-link discrete-link" href="{{ $U('/tasks') }}">
						<i class="fas fa-tasks"></i>
						<span class="nav-link-text">{{ $__t('Tasks') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_BATTERIES)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Batteries overview') }}" data-nav-for-page="batteriesoverview">
					<a class="nav-link discrete-link" href="{{ $U('/batteriesoverview') }}">
						<i class="fas fa-battery-half"></i>
						<span class="nav-link-text">{{ $__t('Batteries overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_EQUIPMENT)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Equipment') }}" data-nav-for-page="equipment">
					<a class="nav-link discrete-link" href="{{ $U('/equipment') }}">
						<i class="fas fa-toolbox"></i>
						<span class="nav-link-text">{{ $__t('Equipment') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_CALENDAR)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Calendar') }}" data-nav-for-page="calendar">
					<a class="nav-link discrete-link" href="{{ $U('/calendar') }}">
						<i class="fas fa-calendar-alt"></i>
						<span class="nav-link-text">{{ $__t('Calendar') }}</span>
					</a>
				</li>
				@endif
				
				@if(GROCY_FEATURE_FLAG_STOCK)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Purchase') }}" data-nav-for-page="purchase">
					<a class="nav-link discrete-link" href="{{ $U('/purchase') }}">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $__t('Purchase') }}</span>
					</a>
				</li>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Consume') }}" data-nav-for-page="consume">
					<a class="nav-link discrete-link" href="{{ $U('/consume') }}">
						<i class="fas fa-utensils"></i>
						<span class="nav-link-text">{{ $__t('Consume') }}</span>
					</a>
				</li>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Transfer') }}" data-nav-for-page="transfer">
					<a class="nav-link discrete-link" href="{{ $U('/transfer') }}">
						<i class="fas fa-exchange-alt"></i>
						<span class="nav-link-text">{{ $__t('Transfer') }}</span>
					</a>
				</li>
				@endif
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Inventory') }}" data-nav-for-page="inventory">
					<a class="nav-link discrete-link" href="{{ $U('/inventory') }}">
						<i class="fas fa-list"></i>
						<span class="nav-link-text">{{ $__t('Inventory') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_CHORES)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Chore tracking') }}" data-nav-for-page="choretracking">
					<a class="nav-link discrete-link" href="{{ $U('/choretracking') }}">
						<i class="fas fa-play"></i>
						<span class="nav-link-text">{{ $__t('Chore tracking') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_BATTERIES)
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Battery tracking') }}" data-nav-for-page="batterytracking">
					<a class="nav-link discrete-link" href="{{ $U('/batterytracking') }}">
						<i class="fas fa-fire"></i>
						<span class="nav-link-text">{{ $__t('Battery tracking') }}</span>
					</a>
				</li>
				@endif

				@php $firstUserentity = true; @endphp
				@foreach($userentitiesForSidebar as $userentity)
				@if($firstUserentity)
				<div class="nav-item-divider"></div>
				@endif
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $userentity->caption }}" data-nav-for-page="userentity-{{ $userentity->name }}">
					<a class="nav-link discrete-link" href="{{ $U('/userobjects/' . $userentity->name) }}">
						<i class="{{ $userentity->icon_css_class }}"></i>
						<span class="nav-link-text">{{ $userentity->caption }}</span>
					</a>
				</li>
				@php if ($firstUserentity) { $firstUserentity = false; } @endphp
				@endforeach
				
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar" data-toggle="tooltip" data-placement="right" title="{{ $__t('Manage master data') }}">
					<a class="nav-link nav-link-collapse collapsed discrete-link" data-toggle="collapse" href="#top-nav-manager-master-data">
						<i class="fas fa-table"></i>
						<span class="nav-link-text">{{ $__t('Manage master data') }}</span>
					</a>
					<ul id="top-nav-manager-master-data" class="sidenav-second-level collapse">
						@if(GROCY_FEATURE_FLAG_STOCK)
						<li data-nav-for-page="products" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/products') }}">
								<i class="fab fa-product-hunt"></i>
								<span class="nav-link-text">{{ $__t('Products') }}</span>
							</a>
						</li>
						@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						<li data-nav-for-page="locations" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/locations') }}">
								<i class="fas fa-map-marker-alt"></i>
								<span class="nav-link-text">{{ $__t('Locations') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
						<li data-nav-for-page="shoppinglocations" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/shoppinglocations') }}">
								<i class="fas fa-shopping-cart"></i>
								<span class="nav-link-text">{{ $__t('Stores') }}</span>
							</a>
						</li>
						@endif
						<li data-nav-for-page="quantityunits" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/quantityunits') }}">
								<i class="fas fa-balance-scale"></i>
								<span class="nav-link-text">{{ $__t('Quantity units') }}</span>
							</a>
						</li>
						<li data-nav-for-page="productgroups" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/productgroups') }}">
								<i class="fas fa-object-group"></i>
								<span class="nav-link-text">{{ $__t('Product groups') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_CHORES)
						<li data-nav-for-page="chores" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/chores') }}">
								<i class="fas fa-home"></i>
								<span class="nav-link-text">{{ $__t('Chores') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_BATTERIES)
						<li data-nav-for-page="batteries" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/batteries') }}">
								<i class="fas fa-battery-half"></i>
								<span class="nav-link-text">{{ $__t('Batteries') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_TASKS)
						<li data-nav-for-page="taskcategories" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/taskcategories') }}">
								<i class="fas fa-project-diagram "></i>
								<span class="nav-link-text">{{ $__t('Task categories') }}</span>
							</a>
						</li>
						@endif
						<li data-nav-for-page="userfields" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/userfields') }}">
								<i class="fas fa-bookmark "></i>
								<span class="nav-link-text">{{ $__t('Userfields') }}</span>
							</a>
						</li>
						<li data-nav-for-page="userentities" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="{{ $U('/userentities') }}">
								<i class="fas fa-bookmark "></i>
								<span class="nav-link-text">{{ $__t('Userentities') }}</span>
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
						<a class="dropdown-item logout-button discrete-link" href="{{ $U('/logout') }}"><i class="fas fa-sign-out-alt"></i>&nbsp;{{ $__t('Logout') }}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item logout-button discrete-link" href="{{ $U('/user/' . GROCY_USER_ID . '?changepw=true') }}"><i class="fas fa-key"></i>&nbsp;{{ $__t('Change password') }}</a>
					</div>
				</li>
				@endif

				@if(GROCY_AUTHENTICATED === true)
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-sliders-h"></i> <span class="d-inline d-lg-none">{{ $__t('View settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-reload-enabled" data-setting-key="auto_reload_on_db_change">
								<label class="form-check-label" for="auto-reload-enabled">
									{{ $__t('Auto reload on external changes') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="show-clock-in-header" data-setting-key="show_clock_in_header">
								<label class="form-check-label" for="show-clock-in-header">
									{{ $__t('Show clock in header') }}
								</label>
							</div>
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="night-mode-enabled" data-setting-key="night_mode_enabled">
								<label class="form-check-label" for="night-mode-enabled">
									{{ $__t('Enable night mode') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-enabled" data-setting-key="auto_night_mode_enabled">
								<label class="form-check-label" for="auto-night-mode-enabled">
									{{ $__t('Auto enable in time range') }}
								</label>
							</div>
							<div class="form-inline">
								<input type="text" class="form-control my-1 user-setting-control" readonly id="auto-night-mode-time-range-from" placeholder="{{ $__t('From') }} ({{ $__t('in format') }} HH:mm)" data-setting-key="auto_night_mode_time_range_from">
								<input type="text" class="form-control user-setting-control" readonly id="auto-night-mode-time-range-to" placeholder="{{ $__t('To') }} ({{ $__t('in format') }} HH:mm)" data-setting-key="auto_night_mode_time_range_to">
							</div>
							<div class="form-check mt-1">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-time-range-goes-over-midgnight" data-setting-key="auto_night_mode_time_range_goes_over_midnight">
								<label class="form-check-label" for="auto-night-mode-time-range-goes-over-midgnight">
									{{ $__t('Time range goes over midnight') }}
								</label>
							</div>
							<input class="form-check-input d-none user-setting-control" type="checkbox" id="currently-inside-night-mode-range" data-setting-key="currently_inside_night_mode_range">
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="keep_screen_on" data-setting-key="keep_screen_on">
								<label class="form-check-label" for="keep_screen_on">
									{{ $__t('Keep screen on') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="keep_screen_on_when_fullscreen_card" data-setting-key="keep_screen_on_when_fullscreen_card">
								<label class="form-check-label" for="keep_screen_on_when_fullscreen_card">
									{{ $__t('Keep screen on while displaying a "fullscreen-card"') }}
								</label>
							</div>
						</div>
					</div>
				</li>
				@endif

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-wrench"></i> <span class="d-inline d-lg-none">{{ $__t('Settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item discrete-link" href="{{ $U('/stocksettings') }}"><i class="fas fa-box"></i>&nbsp;{{ $__t('Stock settings') }}</a>
						@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
						<a class="dropdown-item discrete-link" href="{{ $U('/shoppinglistsettings') }}"><i class="fas fa-shopping-cart"></i>&nbsp;{{ $__t('Shopping list settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_RECIPES)
						<a class="dropdown-item discrete-link" href="{{ $U('/recipessettings') }}"><i class="fas fa-cocktail"></i>&nbsp;{{ $__t('Recipes settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_CHORES)
						<a class="dropdown-item discrete-link" href="{{ $U('/choressettings') }}"><i class="fas fa-home"></i>&nbsp;{{ $__t('Chores settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_BATTERIES)
						<a class="dropdown-item discrete-link" href="{{ $U('/batteriessettings') }}"><i class="fas fa-battery-half"></i>&nbsp;{{ $__t('Batteries settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_TASKS)
						<a class="dropdown-item discrete-link" href="{{ $U('/taskssettings') }}"><i class="fas fa-tasks"></i>&nbsp;{{ $__t('Tasks settings') }}</a>
						@endif
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="{{ $U('/users') }}"><i class="fas fa-users"></i>&nbsp;{{ $__t('Manage users') }}</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="{{ $U('/manageapikeys') }}"><i class="fas fa-handshake"></i>&nbsp;{{ $__t('Manage API keys') }}</a>
						<a class="dropdown-item discrete-link" target="_blank" href="{{ $U('/api') }}"><i class="fas fa-book"></i>&nbsp;{{ $__t('REST API & data model documentation') }}</a>
						<a class="dropdown-item discrete-link" href="{{ $U('/barcodescannertesting') }}"><i class="fas fa-barcode"></i>&nbsp;{{ $__t('Barcode scanner testing') }}</a>
						<div class="dropdown-divider"></div>
						<a id="about-dialog-link" class="dropdown-item discrete-link" href="#"><i class="fas fa-info fa-fw"></i>&nbsp;{{ $__t('About grocy') }} (Version {{ $version }})</a>
					</div>
				</li>
			</ul>
		</div>@endif
	</nav>
	@endif

	<div class="content-wrapper pt-0">
		<div class="container-fluid pr-1 pl-md-3 pl-1">
			<div class="row mb-3">
				<div id="page-content" class="col content-text">
					@yield('content')
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
	@if(!empty($__t('moment_locale') && $__t('moment_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/moment/locale/{{ $__t('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net/js/jquery.dataTables.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive/js/dataTables.responsive.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder/js/dataTables.colReorder.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder-bs4/js/colReorder.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select/js/dataTables.select.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-plugins/filtering/type-based/accent-neutralise.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/timeago/jquery.timeago.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules', true) }}/timeago/locales/jquery.timeago.{{ $__t('timeago_locale') }}.js?v={{ $version }}"></script>
	<script src="{{ $U('/node_modules/toastr/build/toastr.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/sprintf-js/dist/sprintf.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/gettext-translator/src/translator.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/summernote/dist/summernote-bs4.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('summernote_locale') && $__t('summernote_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/summernote/dist/lang/summernote-{{ $__t('summernote_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('bootstrap-select_locale') && $__t('bootstrap-select_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/bootstrap-select/dist/js/i18n/defaults-{{ $__t('bootstrap-select_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/jquery-lazy/jquery.lazy.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/nosleep.js/dist/NoSleep.min.js?v=', true) }}{{ $version }}"></script>

	<script src="{{ $U('/js/extensions.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_dbchangedhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_wakelockhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_nightmode.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_clock.js?v=', true) }}{{ $version }}"></script>
	@stack('pageScripts')
	
	@php
	// @stack('componentScripts') maybe contains the components JS file reference multiple times
	// if the component was included more than once in the view
	//
	// So this is a ugly hack to keep only unique JS file references there

	// The property is normally protected, so change that
	$reflection = new \ReflectionClass($__env);
	$property = $reflection->getProperty('pushes');
	$property->setAccessible(true);
	$env = $property->getValue($__env);

	if (array_key_exists('componentScripts', $env))
	{
		// Take every line into a new array, one element per line
		$filteredStack = array_map(function($value)
		{
			return explode("#SEP#", str_replace(array("\n", "\r", "\t"), '#SEP#', trim($value)));
		}, $env['componentScripts']);

		// Flatten the array into a single one, only keep unique lines, remove empty lines, add a defined new line
		$filteredStack = preg_filter('/$/', "\n", array_filter(array_unique(array_merge(...$filteredStack))));

		// Write it back
		$env['componentScripts'] = $filteredStack;
		$property->setValue($__env, $env);
	}
	@endphp

	@stack('componentScripts')
	@hasSection('viewJsName')<script src="{{ $U('/viewjs', true) }}/@yield('viewJsName').js?v={{ $version }}"></script>@endif

	@if(file_exists(GROCY_DATAPATH . '/custom_js.html'))
		@php include GROCY_DATAPATH . '/custom_js.html' @endphp
	@endif
</body>

</html>
