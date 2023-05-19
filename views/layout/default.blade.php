<!DOCTYPE html>
<html lang="{{ GROCY_LOCALE }}"
	dir="{{ $dir }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1">

	<meta name="robots"
		content="noindex,nofollow">

	<link rel="apple-touch-icon"
		sizes="180x180"
		href="{{ $U('/img/appicons/apple-touch-icon.png?v=', true) }}{{ $version }}">
	<link rel="icon"
		type="image/png"
		sizes="32x32"
		href="{{ $U('/img/appicons/favicon-32x32.png?v=', true) }}{{ $version }}">
	<link rel="icon"
		type="image/png"
		sizes="16x16"
		href="{{ $U('/img/appicons/favicon-16x16.png?v=', true) }}{{ $version }}">
	<link rel="manifest"
		href="{{ $U('/img/appicons/site.webmanifest?v=', true) }}{{ $version }}">
	<link rel="mask-icon"
		href="{{ $U('/img/appicons/safari-pinned-tab.svg?v=', true) }}{{ $version }}"
		color="#0b024c">
	<link rel="shortcut icon"
		href="{{ $U('/img/appicons/favicon.ico?v=', true) }}{{ $version }}">
	<meta name="apple-mobile-web-app-title"
		content="grocy">
	<meta name="application-name"
		content="grocy">
	<meta name="msapplication-TileColor"
		content="#e5e5e5">
	<meta name="msapplication-config"
		content="{{ $U('/img/appicons/browserconfig.xml?v=', true) }}{{ $version }}">
	<meta name="theme-color"
		content="#ffffff">

	<title>@yield('title') | grocy</title>
	<link href="{{ $U('/node_modules/bootstrap/dist/css/bootstrap.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/@fortawesome/fontawesome-free/css/all.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/css/bootstrap-combobox.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-colreorder-bs4/css/colReorder.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/toastr/build/toastr.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/summernote/dist/summernote-bs4.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/node_modules/@fontsource/noto-sans/latin.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/css/grocy_menu_layout.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/css/grocy.css?v=', true) }}{{ $version }}"
		rel="stylesheet">

	@if(boolval($userSettings['night_mode_enabled_internal']))
	<link id="night-mode-stylesheet"
		href="{{ $U('/css/grocy_night_mode.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif

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
		Grocy.Currency = '{{ GROCY_CURRENCY }}';
		Grocy.EnergyUnit = '{{ GROCY_ENERGY_UNIT }}';
		Grocy.CalendarFirstDayOfWeek = '{{ GROCY_CALENDAR_FIRST_DAY_OF_WEEK }}';
		Grocy.CalendarShowWeekNumbers = {{ BoolToString(GROCY_CALENDAR_SHOW_WEEK_OF_YEAR) }};
		Grocy.LocalizationStrings = {!! $LocalizationStrings !!};
		Grocy.LocalizationStringsQu = {!! $LocalizationStringsQu !!};
		Grocy.FeatureFlags = {!! json_encode($featureFlags) !!};
		Grocy.Webhooks = {
		@if(GROCY_FEATURE_FLAG_LABEL_PRINTER && !GROCY_LABEL_PRINTER_RUN_SERVER)
			"labelprinter" : {
				"hook": "{{ GROCY_LABEL_PRINTER_WEBHOOK }}",
				"extra_data": {!! json_encode(GROCY_LABEL_PRINTER_PARAMS) !!},
				"json": {{ BoolToString(GROCY_LABEL_PRINTER_HOOK_JSON) }}
			}
		@endif
		};

		@if (GROCY_AUTHENTICATED)
		Grocy.UserSettings = {!! json_encode($userSettings) !!};
		Grocy.UserId = {{ GROCY_USER_ID }};
		Grocy.UserPermissions = {!! json_encode($permissions) !!};
		@else
		Grocy.UserSettings = { };
		Grocy.UserId = -1;
		@endif
	</script>
</head>

<body class="fixed-nav @if(boolval($userSettings['night_mode_enabled_internal'])) night-mode @endif @if($embedded) embedded @endif">
	@if(!($embedded))
	<nav id="mainNav"
		class="navbar navbar-expand-lg navbar-light fixed-top">
		<a class="navbar-brand py-0"
			href="{{ $U('/') }}"><img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}"
				height="30"></a>
		<span id="clock-container"
			class="text-muted font-italic d-none">
			<i class="fa-regular fa-clock"></i>
			<span id="clock-small"
				class="d-inline d-sm-none"></span>
			<span id="clock-big"
				class="d-none d-sm-inline"></span>
		</span>

		@if(GROCY_AUTHENTICATED)
		<button class="navbar-toggler navbar-toggler-right"
			type="button"
			data-toggle="collapse"
			data-target="#sidebarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div id="sidebarResponsive"
			class="collapse navbar-collapse">
			<ul class="navbar-nav navbar-sidenav">

				@if(GROCY_FEATURE_FLAG_STOCK)
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Stock overview') }}"
					data-nav-for-page="stockoverview">
					<a class="nav-link discrete-link"
						href="{{ $U('/stockoverview') }}">
						<i class="fa-solid fa-box"></i>
						<span class="nav-link-text">{{ $__t('Stock overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Shopping list') }}"
					data-nav-for-page="shoppinglist">
					<a class="nav-link discrete-link"
						href="{{ $U('/shoppinglist') }}">
						<i class="fa-solid fa-shopping-cart"></i>
						<span class="nav-link-text">{{ $__t('Shopping list') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_RECIPES)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar permission-RECIPES"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Recipes') }}"
					data-nav-for-page="recipes">
					<a class="nav-link discrete-link"
						href="{{ $U('/recipes') }}">
						<i class="fa-solid fa-pizza-slice"></i>
						<span class="nav-link-text">{{ $__t('Recipes') }}</span>
					</a>
				</li>
				@if(GROCY_FEATURE_FLAG_RECIPES_MEALPLAN)
				<li class="nav-item nav-item-sidebar permission-RECIPES_MEALPLAN"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Meal plan') }}"
					data-nav-for-page="mealplan">
					<a id="meal-plan-nav-link"
						class="nav-link discrete-link"
						href="{{ $U('/mealplan') }}">
						<i class="fa-solid fa-paper-plane"></i>
						<span class="nav-link-text">{{ $__t('Meal plan') }}</span>
					</a>
				</li>
				@endif
				@endif
				@if(GROCY_FEATURE_FLAG_CHORES)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Chores overview') }}"
					data-nav-for-page="choresoverview">
					<a class="nav-link discrete-link"
						href="{{ $U('/choresoverview') }}">
						<i class="fa-solid fa-home"></i>
						<span class="nav-link-text">{{ $__t('Chores overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_TASKS)
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Tasks') }}"
					data-nav-for-page="tasks">
					<a class="nav-link discrete-link"
						href="{{ $U('/tasks') }}">
						<i class="fa-solid fa-tasks"></i>
						<span class="nav-link-text">{{ $__t('Tasks') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_BATTERIES)
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Batteries overview') }}"
					data-nav-for-page="batteriesoverview">
					<a class="nav-link discrete-link"
						href="{{ $U('/batteriesoverview') }}">
						<i class="fa-solid fa-battery-half"></i>
						<span class="nav-link-text">{{ $__t('Batteries overview') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_EQUIPMENT)
				<li class="nav-item nav-item-sidebar permission-EQUIPMENT"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Equipment') }}"
					data-nav-for-page="equipment">
					<a class="nav-link discrete-link"
						href="{{ $U('/equipment') }}">
						<i class="fa-solid fa-toolbox"></i>
						<span class="nav-link-text">{{ $__t('Equipment') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_CALENDAR)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar permission-CALENDAR"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Calendar') }}"
					data-nav-for-page="calendar">
					<a class="nav-link discrete-link"
						href="{{ $U('/calendar') }}">
						<i class="fa-solid fa-calendar-days"></i>
						<span class="nav-link-text">{{ $__t('Calendar') }}</span>
					</a>
				</li>
				@endif

				@if(GROCY_FEATURE_FLAG_STOCK)
				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar permission-STOCK_PURCHASE"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Purchase') }}"
					data-nav-for-page="purchase">
					<a class="nav-link discrete-link"
						href="{{ $U('/purchase') }}">
						<i class="fa-solid fa-cart-plus"></i>
						<span class="nav-link-text">{{ $__t('Purchase') }}</span>
					</a>
				</li>
				<li class="nav-item nav-item-sidebar permission-STOCK_CONSUME"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Consume') }}"
					data-nav-for-page="consume">
					<a class="nav-link discrete-link"
						href="{{ $U('/consume') }}">
						<i class="fa-solid fa-utensils"></i>
						<span class="nav-link-text">{{ $__t('Consume') }}</span>
					</a>
				</li>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				<li class="nav-item nav-item-sidebar permission-STOCK_TRANSFER"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Transfer') }}"
					data-nav-for-page="transfer">
					<a class="nav-link discrete-link"
						href="{{ $U('/transfer') }}">
						<i class="fa-solid fa-exchange-alt"></i>
						<span class="nav-link-text">{{ $__t('Transfer') }}</span>
					</a>
				</li>
				@endif
				<li class="nav-item nav-item-sidebar permission-STOCK_INVENTORY"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Inventory') }}"
					data-nav-for-page="inventory">
					<a class="nav-link discrete-link"
						href="{{ $U('/inventory') }}">
						<i class="fa-solid fa-list"></i>
						<span class="nav-link-text">{{ $__t('Inventory') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_CHORES)
				<li class="nav-item nav-item-sidebar permission-CHORE_TRACK_EXECUTION"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Chore tracking') }}"
					data-nav-for-page="choretracking">
					<a class="nav-link discrete-link"
						href="{{ $U('/choretracking') }}">
						<i class="fa-solid fa-play"></i>
						<span class="nav-link-text">{{ $__t('Chore tracking') }}</span>
					</a>
				</li>
				@endif
				@if(GROCY_FEATURE_FLAG_BATTERIES)
				<li class="nav-item nav-item-sidebar permission-BATTERIES_TRACK_CHARGE_CYCLE"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Battery tracking') }}"
					data-nav-for-page="batterytracking">
					<a class="nav-link discrete-link"
						href="{{ $U('/batterytracking') }}">
						<i class="fa-solid fa-car-battery"></i>
						<span class="nav-link-text">{{ $__t('Battery tracking') }}</span>
					</a>
				</li>
				@endif

				@php $firstUserentity = true; @endphp
				@foreach($userentitiesForSidebar as $userentity)
				@if($firstUserentity)
				<div class="nav-item-divider"></div>
				@endif
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $userentity->caption }}"
					data-nav-for-page="userentity-{{ $userentity->name }}">
					<a class="nav-link discrete-link"
						href="{{ $U('/userobjects/' . $userentity->name) }}">
						<i class="{{ $userentity->icon_css_class }}"></i>
						<span class="nav-link-text">{{ $userentity->caption }}</span>
					</a>
				</li>
				@php if ($firstUserentity) { $firstUserentity = false; } @endphp
				@endforeach

				<div class="nav-item-divider"></div>
				<li class="nav-item nav-item-sidebar"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Manage master data') }}">
					<a class="nav-link nav-link-collapse collapsed discrete-link"
						data-toggle="collapse"
						href="#top-nav-manager-master-data">
						<i class="fa-solid fa-table"></i>
						<span class="nav-link-text">{{ $__t('Manage master data') }}</span>
					</a>
					<ul id="top-nav-manager-master-data"
						class="sidenav-second-level collapse">
						<li data-nav-for-page="products"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/products') }}">
								<span class="nav-link-text">{{ $__t('Products') }}</span>
							</a>
						</li>
						@if(GROCY_FEATURE_FLAG_STOCK)
						@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						<li data-nav-for-page="locations"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/locations') }}">
								<span class="nav-link-text">{{ $__t('Locations') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
						<li data-nav-for-page="shoppinglocations"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/shoppinglocations') }}">
								<span class="nav-link-text">{{ $__t('Stores') }}</span>
							</a>
						</li>
						@endif
						@endif
						<li data-nav-for-page="quantityunits"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/quantityunits') }}">
								<span class="nav-link-text">{{ $__t('Quantity units') }}</span>
							</a>
						</li>
						<li data-nav-for-page="productgroups"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/productgroups') }}">
								<span class="nav-link-text">{{ $__t('Product groups') }}</span>
							</a>
						</li>
						@if(GROCY_FEATURE_FLAG_CHORES)
						<li data-nav-for-page="chores"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/chores') }}">
								<span class="nav-link-text">{{ $__t('Chores') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_BATTERIES)
						<li data-nav-for-page="batteries"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/batteries') }}">
								<span class="nav-link-text">{{ $__t('Batteries') }}</span>
							</a>
						</li>
						@endif
						@if(GROCY_FEATURE_FLAG_TASKS)
						<li data-nav-for-page="taskcategories"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/taskcategories') }}">
								<span class="nav-link-text">{{ $__t('Task categories') }}</span>
							</a>
						</li>
						@endif
						<li data-nav-for-page="userfields"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/userfields') }}">
								<span class="nav-link-text">{{ $__t('Userfields') }}</span>
							</a>
						</li>
						<li data-nav-for-page="userentities"
							data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link"
								href="{{ $U('/userentities') }}">
								<span class="nav-link-text">{{ $__t('Userentities') }}</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>

			<ul class="navbar-nav sidenav-toggler">
				<li class="nav-item">
					<a id="sidenavToggler"
						class="nav-link text-center">
						<i class="fa-solid fa-angle-left"></i>
					</a>
				</li>
			</ul>

			<ul class="navbar-nav ml-auto">
				@if(GROCY_AUTHENTICATED && !GROCY_IS_EMBEDDED_INSTALL && !GROCY_DISABLE_AUTH)
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link @if(!empty(GROCY_USER_PICTURE_FILE_NAME)) py-0 @endif"
						href="#"
						data-toggle="dropdown">
						@if(empty(GROCY_USER_PICTURE_FILE_NAME))
						<i class="fa-solid fa-user"></i>
						@else
						<img class="rounded-circle"
							src="{{ $U('/files/userpictures/' . base64_encode(GROCY_USER_PICTURE_FILE_NAME) . '_' . base64_encode(GROCY_USER_PICTURE_FILE_NAME) . '?force_serve_as=picture&best_fit_width=32&best_fit_height=32') }}">
						@endif
						{{ GROCY_USER_USERNAME }}
					</a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item logout-button discrete-link"
							href="{{ $U('/logout') }}"><i class="fa-solid fa-sign-out-alt"></i>&nbsp;{{ $__t('Logout') }}</a>
						<div class="dropdown-divider"></div>
						@if(!defined('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION'))
						<a class="dropdown-item logout-button discrete-link"
							href="{{ $U('/user/' . GROCY_USER_ID . '?changepw=true') }}"><i class="fa-solid fa-key"></i>&nbsp;{{ $__t('Change password') }}</a>
						@else
						<a class="dropdown-item logout-button discrete-link"
							href="{{ $U('/user/' . GROCY_USER_ID) }}"><i class="fa-solid fa-key"></i>&nbsp;{{ $__t('Edit user') }}</a>
						@endif
					</div>
				</li>
				@endif

				@if(GROCY_AUTHENTICATED)
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link"
						href="#"
						data-toggle="dropdown"><i class="fa-solid fa-sliders-h"></i> <span class="d-inline d-lg-none">{{ $__t('View settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="auto-reload-enabled"
									data-setting-key="auto_reload_on_db_change">
								<label class="form-check-label"
									for="auto-reload-enabled">
									{{ $__t('Auto reload on external changes') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="show-clock-in-header"
									data-setting-key="show_clock_in_header">
								<label class="form-check-label"
									for="show-clock-in-header">
									{{ $__t('Show clock in header') }}
								</label>
							</div>
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item pt-0">
							<div>
								{{ $__t('Night mode') }}
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input user-setting-control"
									type="radio"
									name="night-mode"
									id="night-mode-on"
									value="on"
									data-setting-key="night_mode">
								<label class="custom-control-label"
									for="night-mode-on">{{ $__t('On') }}</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input user-setting-control"
									type="radio"
									name="night-mode"
									id="night-mode-follow-system"
									value="follow-system"
									data-setting-key="night_mode">
								<label class="custom-control-label"
									for="night-mode-follow-system">{{ $__t('Use system setting') }}</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input class="custom-control-input user-setting-control"
									type="radio"
									name="night-mode"
									id="night-mode-off"
									value="off"
									data-setting-key="night_mode">
								<label class="custom-control-label"
									for="night-mode-off">{{ $__t('Off') }}</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="auto-night-mode-enabled"
									data-setting-key="auto_night_mode_enabled">
								<label class="form-check-label"
									for="auto-night-mode-enabled">
									{{ $__t('Auto enable in time range') }}
								</label>
							</div>
							<div class="form-inline">
								<input type="text"
									class="form-control my-1 user-setting-control"
									readonly
									id="auto-night-mode-time-range-from"
									placeholder="{{ $__t('From') }} ({{ $__t('in format') }} HH:mm)"
									data-setting-key="auto_night_mode_time_range_from">
								<input type="text"
									class="form-control user-setting-control"
									readonly
									id="auto-night-mode-time-range-to"
									placeholder="{{ $__t('To') }} ({{ $__t('in format') }} HH:mm)"
									data-setting-key="auto_night_mode_time_range_to">
							</div>
							<div class="form-check mt-1">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="auto-night-mode-time-range-goes-over-midgnight"
									data-setting-key="auto_night_mode_time_range_goes_over_midnight">
								<label class="form-check-label"
									for="auto-night-mode-time-range-goes-over-midgnight">
									{{ $__t('Time range goes over midnight') }}
								</label>
							</div>
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="keep_screen_on"
									data-setting-key="keep_screen_on">
								<label class="form-check-label"
									for="keep_screen_on">
									{{ $__t('Keep screen on') }}
								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control"
									type="checkbox"
									id="keep_screen_on_when_fullscreen_card"
									data-setting-key="keep_screen_on_when_fullscreen_card">
								<label class="form-check-label"
									for="keep_screen_on_when_fullscreen_card">
									{{ $__t('Keep screen on while displaying a "fullscreen-card"') }}
								</label>
							</div>
						</div>
					</div>
				</li>
				@endif

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link"
						href="#"
						data-toggle="dropdown"><i class="fa-solid fa-wrench"></i> <span class="d-inline d-lg-none">{{ $__t('Settings') }}</span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item discrete-link"
							href="{{ $U('/stocksettings') }}"><i class="fa-solid fa-box"></i>&nbsp;{{ $__t('Stock settings') }}</a>
						@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
						<a class="dropdown-item discrete-link permission-SHOPPINGLIST"
							href="{{ $U('/shoppinglistsettings') }}"><i class="fa-solid fa-shopping-cart"></i>&nbsp;{{ $__t('Shopping list settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_RECIPES)
						<a class="dropdown-item discrete-link permission-RECIPES"
							href="{{ $U('/recipessettings') }}"><i class="fa-solid fa-pizza-slice"></i>&nbsp;{{ $__t('Recipes settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_CHORES)
						<a class="dropdown-item discrete-link permission-CHORES"
							href="{{ $U('/choressettings') }}"><i class="fa-solid fa-home"></i>&nbsp;{{ $__t('Chores settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_TASKS)
						<a class="dropdown-item discrete-link permission-TASKS"
							href="{{ $U('/taskssettings') }}"><i class="fa-solid fa-tasks"></i>&nbsp;{{ $__t('Tasks settings') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_BATTERIES)
						<a class="dropdown-item discrete-link permission-BATTERIES"
							href="{{ $U('/batteriessettings') }}"><i class="fa-solid fa-battery-half"></i>&nbsp;{{ $__t('Batteries settings') }}</a>
						@endif
						<div class="dropdown-divider"></div>
						<a data-href="{{ $U('/usersettings') }}"
							class="dropdown-item discrete-link link-return">
							<i class="fa-solid fa-user-cog"></i> {{ $__t('User settings') }}
						</a>
						@if(!GROCY_IS_EMBEDDED_INSTALL && !GROCY_DISABLE_AUTH)
						<a class="dropdown-item discrete-link permission-USERS_READ"
							href="{{ $U('/users') }}"><i class="fa-solid fa-users"></i>&nbsp;{{ $__t('Manage users') }}</a>
						@endif
						<div class="dropdown-divider"></div>
						@if(!GROCY_DISABLE_AUTH)
						<a class="dropdown-item discrete-link"
							href="{{ $U('/manageapikeys') }}"><i class="fa-solid fa-handshake"></i>&nbsp;{{ $__t('Manage API keys') }}</a>
						@endif
						<a class="dropdown-item discrete-link"
							target="_blank"
							href="{{ $U('/api') }}"><i class="fa-solid fa-book"></i>&nbsp;{{ $__t('REST API browser') }}</a>
						<a class="dropdown-item discrete-link"
							href="{{ $U('/barcodescannertesting') }}"><i class="fa-solid fa-barcode"></i>&nbsp;{{ $__t('Barcode scanner testing') }}</a>
						<div class="dropdown-divider"></div>
						<a id="about-dialog-link"
							class="dropdown-item discrete-link"
							href="#"><i class="fa-solid fa-info fa-fw"></i>&nbsp;{{ $__t('About grocy') }} (Version {{ $version }})</a>
					</div>
				</li>
			</ul>
		</div>@endif
	</nav>
	@endif

	<div class="@if(GROCY_AUTHENTICATED) content-wrapper @endif pt-0">
		<div class="container-fluid @if(GROCY_AUTHENTICATED) pr-1 pl-md-3 pl-1 @endif">
			<div class="row mb-3">
				<div id="page-content"
					class="col content-text">
					@yield('content')
				</div>
			</div>
		</div>
	</div>

	<script src="{{ $U('/node_modules/jquery/dist/jquery.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/bootbox/dist/bootbox.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/jquery-serializejson/jquery.serializejson.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/moment/min/moment.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('moment_locale') && $__t('moment_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/moment/locale/{{ $__t('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net/js/jquery.dataTables.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder/js/dataTables.colReorder.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-colreorder-bs4/js/colReorder.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-plugins/filtering/type-based/accent-neutralise.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-plugins/sorting/chinese-string.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select/js/dataTables.select.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/toastr/build/toastr.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/gettext-translator/dist/translator.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/summernote/dist/summernote-bs4.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('summernote_locale') && $__t('summernote_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/summernote/dist/lang/summernote-{{ $__t('summernote_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('bootstrap-select_locale') && $__t('bootstrap-select_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/bootstrap-select/dist/js/i18n/defaults-{{ $__t('bootstrap-select_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/node_modules/jquery-lazy/jquery.lazy.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/nosleep.js/dist/NoSleep.min.js?v=', true) }}{{ $version }}"></script>

	<script src="{{ $U('/js/extensions.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_menu_layout.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_dbchangedhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_wakelockhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_nightmode.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_clock.js?v=', true) }}{{ $version }}"></script>
	@stack('pageScripts')
	@stack('componentScripts')
	@hasSection('viewJsName')<script src="{{ $U('/viewjs', true) }}/@yield('viewJsName').js?v={{ $version }}"></script>@endif

	@if(file_exists(GROCY_DATAPATH . '/custom_js.html'))
	@php include GROCY_DATAPATH . '/custom_js.html' @endphp
	@endif
</body>

</html>
