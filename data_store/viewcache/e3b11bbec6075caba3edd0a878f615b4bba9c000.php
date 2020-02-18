<!DOCTYPE html>
<html lang="<?php echo e(GROCY_CULTURE); ?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">

	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($U('/img/appicons/apple-touch-icon.png?v=', true)); ?><?php echo e($version); ?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($U('/img/appicons/favicon-32x32.png?v=', true)); ?><?php echo e($version); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($U('/img/appicons/favicon-16x16.png?v=', true)); ?><?php echo e($version); ?>">
	<link rel="manifest" href="<?php echo e($U('/img/appicons/site.webmanifest?v=', true)); ?><?php echo e($version); ?>">
	<link rel="mask-icon" href="<?php echo e($U('/img/appicons/safari-pinned-tab.svg?v=', true)); ?><?php echo e($version); ?>" color="#0b024c">
	<link rel="shortcut icon" href="<?php echo e($U('/img/appicons/favicon.ico?v=', true)); ?><?php echo e($version); ?>">
	<meta name="apple-mobile-web-app-title" content="grocy">
	<meta name="application-name" content="grocy">
	<meta name="msapplication-TileColor" content="#e5e5e5">
	<meta name="msapplication-config" content="<?php echo e($U('/img/appicons/browserconfig.xml?v=', true)); ?><?php echo e($version); ?>">
	<meta name="theme-color" content="#ffffff">

	<title><?php echo $__env->yieldContent('title'); ?> | grocy</title>

	<link href="<?php echo e($U('/node_modules/bootstrap/dist/css/bootstrap.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/startbootstrap-sb-admin/css/sb-admin.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/@fortawesome/fontawesome-free/css/all.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/@danielfarrell/bootstrap-combobox/css/bootstrap-combobox.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/datatables.net-colreorder-bs4/css/colReorder.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/toastr/build/toastr.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">	
	<link href="<?php echo e($U('/node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/summernote/dist/summernote-bs4.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/node_modules/bootstrap-select/dist/css/bootstrap-select.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/components_unmanaged/noto-sans-v9-latin/noto-sans-v9-latin.min.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/css/grocy.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<link href="<?php echo e($U('/css/grocy_night_mode.css?v=', true)); ?><?php echo e($version); ?>" rel="stylesheet">
	<?php echo $__env->yieldPushContent('pageStyles'); ?>

	<?php if(file_exists(GROCY_DATAPATH . '/custom_css.html')): ?>
		<?php include GROCY_DATAPATH . '/custom_css.html' ?>
	<?php endif; ?>

	<script>
		var Grocy = { };
		Grocy.Components = { };
		Grocy.Mode = '<?php echo e(GROCY_MODE); ?>';
		Grocy.BaseUrl = '<?php echo e($U('/')); ?>';
		Grocy.CurrentUrlRelative = "/" + window.location.toString().replace(Grocy.BaseUrl, "");
		Grocy.ActiveNav = '<?php echo $__env->yieldContent('activeNav', ''); ?>';
		Grocy.Culture = '<?php echo e(GROCY_CULTURE); ?>';
		Grocy.Currency = '<?php echo e(GROCY_CURRENCY); ?>';
		Grocy.CalendarFirstDayOfWeek = '<?php echo e(GROCY_CALENDAR_FIRST_DAY_OF_WEEK); ?>';
		Grocy.CalendarShowWeekNumbers = <?php echo e(BoolToString(GROCY_CALENDAR_SHOW_WEEK_OF_YEAR)); ?>;
		Grocy.GettextPo = <?php echo $GettextPo; ?>;
		Grocy.FeatureFlags = <?php echo json_encode($featureFlags); ?>;

		<?php if(GROCY_AUTHENTICATED): ?>
		Grocy.UserSettings = <?php echo json_encode($userSettings); ?>;
		Grocy.UserId = <?php echo e(GROCY_USER_ID); ?>;
		<?php else: ?>
		Grocy.UserSettings = { };
		Grocy.UserId = -1;
		<?php endif; ?>
	</script>
</head>

<body class="fixed-nav <?php if(boolval($userSettings['night_mode_enabled']) || (boolval($userSettings['auto_night_mode_enabled']) && boolval($userSettings['currently_inside_night_mode_range']))): ?> night-mode <?php endif; ?> <?php if($embedded): ?> embedded <?php endif; ?>">
	<?php if(!($embedded)): ?>
	<nav id="mainNav" class="navbar navbar-expand-lg navbar-light fixed-top">
		<a class="navbar-brand py-0" href="<?php echo e($U('/')); ?>"><img src="<?php echo e($U('/img/grocy_logo.svg?v=', true)); ?><?php echo e($version); ?>" height="30"></a>
		<span id="clock-container" class="text-muted font-italic d-none">
			<i class="far fa-clock"></i>
			<span id="clock-small" class="d-inline d-sm-none"></span>
			<span id="clock-big" class="d-none d-sm-inline"></span>
		</span>
		
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#sidebarResponsive">
			<span class="navbar-toggler-icon"></span>
		</button>

		<?php if(GROCY_AUTHENTICATED): ?>
		<div id="sidebarResponsive" class="collapse navbar-collapse">
			<ul class="navbar-nav navbar-sidenav pt-2">

				<?php if(GROCY_FEATURE_FLAG_STOCK): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Stock overview')); ?>" data-nav-for-page="stockoverview">
					<a class="nav-link discrete-link" href="<?php echo e($U('/stockoverview')); ?>">
						<i class="fas fa-box"></i>
						<span class="nav-link-text"><?php echo e($__t('Stock overview')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_SHOPPINGLIST): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Shopping list')); ?>" data-nav-for-page="shoppinglist">
					<a class="nav-link discrete-link" href="<?php echo e($U('/shoppinglist')); ?>">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text"><?php echo e($__t('Shopping list')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_RECIPES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Recipes')); ?>" data-nav-for-page="recipes">
					<a class="nav-link discrete-link" href="<?php echo e($U('/recipes')); ?>">
						<i class="fas fa-cocktail"></i>
						<span class="nav-link-text"><?php echo e($__t('Recipes')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_CHORES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Chores overview')); ?>" data-nav-for-page="choresoverview">
					<a class="nav-link discrete-link" href="<?php echo e($U('/choresoverview')); ?>">
						<i class="fas fa-home"></i>
						<span class="nav-link-text"><?php echo e($__t('Chores overview')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_TASKS): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Tasks')); ?>" data-nav-for-page="tasks">
					<a class="nav-link discrete-link" href="<?php echo e($U('/tasks')); ?>">
						<i class="fas fa-tasks"></i>
						<span class="nav-link-text"><?php echo e($__t('Tasks')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_BATTERIES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Batteries overview')); ?>" data-nav-for-page="batteriesoverview">
					<a class="nav-link discrete-link" href="<?php echo e($U('/batteriesoverview')); ?>">
						<i class="fas fa-battery-half"></i>
						<span class="nav-link-text"><?php echo e($__t('Batteries overview')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_EQUIPMENT): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Equipment')); ?>" data-nav-for-page="equipment">
					<a class="nav-link discrete-link" href="<?php echo e($U('/equipment')); ?>">
						<i class="fas fa-toolbox"></i>
						<span class="nav-link-text"><?php echo e($__t('Equipment')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				
				<?php if(GROCY_FEATURE_FLAG_STOCK): ?>
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Purchase')); ?>" data-nav-for-page="purchase">
					<a class="nav-link discrete-link" href="<?php echo e($U('/purchase')); ?>">
						<i class="fas fa-shopping-cart"></i>
						<span class="nav-link-text"><?php echo e($__t('Purchase')); ?></span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Consume')); ?>" data-nav-for-page="consume">
					<a class="nav-link discrete-link" href="<?php echo e($U('/consume')); ?>">
						<i class="fas fa-utensils"></i>
						<span class="nav-link-text"><?php echo e($__t('Consume')); ?></span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Inventory')); ?>" data-nav-for-page="inventory">
					<a class="nav-link discrete-link" href="<?php echo e($U('/inventory')); ?>">
						<i class="fas fa-list"></i>
						<span class="nav-link-text"><?php echo e($__t('Inventory')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_CHORES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Chore tracking')); ?>" data-nav-for-page="choretracking">
					<a class="nav-link discrete-link" href="<?php echo e($U('/choretracking')); ?>">
						<i class="fas fa-play"></i>
						<span class="nav-link-text"><?php echo e($__t('Chore tracking')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_BATTERIES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Battery tracking')); ?>" data-nav-for-page="batterytracking">
					<a class="nav-link discrete-link" href="<?php echo e($U('/batterytracking')); ?>">
						<i class="fas fa-fire"></i>
						<span class="nav-link-text"><?php echo e($__t('Battery tracking')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_CALENDAR): ?>
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Calendar')); ?>" data-nav-for-page="calendar">
					<a class="nav-link discrete-link" href="<?php echo e($U('/calendar')); ?>">
						<i class="fas fa-calendar-alt"></i>
						<span class="nav-link-text"><?php echo e($__t('Calendar')); ?></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if(GROCY_FEATURE_FLAG_RECIPES): ?>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Meal plan')); ?>" data-nav-for-page="mealplan">
					<a class="nav-link discrete-link" href="<?php echo e($U('/mealplan')); ?>">
						<i class="fas fa-paper-plane"></i>
						<span class="nav-link-text"><?php echo e($__t('Meal plan')); ?></span>
					</a>
				</li>
				<?php endif; ?>

				<?php $firstUserentity = true; ?>
				<?php $__currentLoopData = $userentitiesForSidebar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userentity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<li class="nav-item <?php if($firstUserentity): ?> mt-4 <?php endif; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo e($userentity->caption); ?>" data-nav-for-page="userentity-<?php echo e($userentity->name); ?>">
					<a class="nav-link discrete-link" href="<?php echo e($U('/userobjects/' . $userentity->name)); ?>">
						<i class="<?php echo e($userentity->icon_css_class); ?>"></i>
						<span class="nav-link-text"><?php echo e($userentity->caption); ?></span>
					</a>
				</li>
				<?php if ($firstUserentity) { $firstUserentity = false; } ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
				<li class="nav-item mt-4" data-toggle="tooltip" data-placement="right" title="<?php echo e($__t('Manage master data')); ?>">
					<a class="nav-link nav-link-collapse collapsed discrete-link" data-toggle="collapse" href="#top-nav-manager-master-data">
						<i class="fas fa-table"></i>
						<span class="nav-link-text"><?php echo e($__t('Manage master data')); ?></span>
					</a>
					<ul id="top-nav-manager-master-data" class="sidenav-second-level collapse">
						<?php if(GROCY_FEATURE_FLAG_STOCK): ?>
						<li data-nav-for-page="products" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/products')); ?>">
								<i class="fab fa-product-hunt"></i>
								<span class="nav-link-text"><?php echo e($__t('Products')); ?></span>
							</a>
						</li>
						<?php if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING): ?>
						<li data-nav-for-page="locations" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/locations')); ?>">
								<i class="fas fa-map-marker-alt"></i>
								<span class="nav-link-text"><?php echo e($__t('Locations')); ?></span>
							</a>
						</li>
						<?php endif; ?>
						<li data-nav-for-page="quantityunits" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/quantityunits')); ?>">
								<i class="fas fa-balance-scale"></i>
								<span class="nav-link-text"><?php echo e($__t('Quantity units')); ?></span>
							</a>
						</li>
						<li data-nav-for-page="productgroups" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/productgroups')); ?>">
								<i class="fas fa-object-group"></i>
								<span class="nav-link-text"><?php echo e($__t('Product groups')); ?></span>
							</a>
						</li>
						<?php endif; ?>
						<?php if(GROCY_FEATURE_FLAG_CHORES): ?>
						<li data-nav-for-page="chores" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/chores')); ?>">
								<i class="fas fa-home"></i>
								<span class="nav-link-text"><?php echo e($__t('Chores')); ?></span>
							</a>
						</li>
						<?php endif; ?>
						<?php if(GROCY_FEATURE_FLAG_BATTERIES): ?>
						<li data-nav-for-page="batteries" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/batteries')); ?>">
								<i class="fas fa-battery-half"></i>
								<span class="nav-link-text"><?php echo e($__t('Batteries')); ?></span>
							</a>
						</li>
						<?php endif; ?>
						<?php if(GROCY_FEATURE_FLAG_TASKS): ?>
						<li data-nav-for-page="taskcategories" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/taskcategories')); ?>">
								<i class="fas fa-project-diagram "></i>
								<span class="nav-link-text"><?php echo e($__t('Task categories')); ?></span>
							</a>
						</li>
						<?php endif; ?>
						<li data-nav-for-page="userfields" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/userfields')); ?>">
								<i class="fas fa-bookmark "></i>
								<span class="nav-link-text"><?php echo e($__t('Userfields')); ?></span>
							</a>
						</li>
						<li data-nav-for-page="userentities" data-sub-menu-of="#top-nav-manager-master-data">
							<a class="nav-link discrete-link" href="<?php echo e($U('/userentities')); ?>">
								<i class="fas fa-bookmark "></i>
								<span class="nav-link-text"><?php echo e($__t('Userentities')); ?></span>
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
				<?php if(GROCY_AUTHENTICATED === true && !GROCY_IS_EMBEDDED_INSTALL): ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-user"></i> <?php echo e(GROCY_USER_USERNAME); ?></a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item logout-button discrete-link" href="<?php echo e($U('/logout')); ?>"><i class="fas fa-sign-out-alt"></i>&nbsp;<?php echo e($__t('Logout')); ?></a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item logout-button discrete-link" href="<?php echo e($U('/user/' . GROCY_USER_ID . '?changepw=true')); ?>"><i class="fas fa-key"></i>&nbsp;<?php echo e($__t('Change password')); ?></a>
					</div>
				</li>
				<?php endif; ?>

				<?php if(GROCY_AUTHENTICATED === true): ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-sliders-h"></i> <span class="d-inline d-lg-none"><?php echo e($__t('View settings')); ?></span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-reload-enabled" data-setting-key="auto_reload_on_db_change">
								<label class="form-check-label" for="auto-reload-enabled">
									<?php echo e($__t('Auto reload on external changes')); ?>

								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="show-clock-in-header" data-setting-key="show_clock_in_header">
								<label class="form-check-label" for="show-clock-in-header">
									<?php echo e($__t('Show clock in header')); ?>

								</label>
							</div>
						</div>
						<div class="dropdown-divider"></div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="night-mode-enabled" data-setting-key="night_mode_enabled">
								<label class="form-check-label" for="night-mode-enabled">
									<?php echo e($__t('Enable night mode')); ?>

								</label>
							</div>
						</div>
						<div class="dropdown-item">
							<div class="form-check">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-enabled" data-setting-key="auto_night_mode_enabled">
								<label class="form-check-label" for="auto-night-mode-enabled">
									<?php echo e($__t('Auto enable in time range')); ?>

								</label>
							</div>
							<div class="form-inline">
								<input type="text" class="form-control my-1 user-setting-control" readonly id="auto-night-mode-time-range-from" placeholder="<?php echo e($__t('From')); ?> (<?php echo e($__t('in format')); ?> HH:mm)" data-setting-key="auto_night_mode_time_range_from">
								<input type="text" class="form-control user-setting-control" readonly id="auto-night-mode-time-range-to" placeholder="<?php echo e($__t('To')); ?> (<?php echo e($__t('in format')); ?> HH:mm)" data-setting-key="auto_night_mode_time_range_to">
							</div>
							<div class="form-check mt-1">
								<input class="form-check-input user-setting-control" type="checkbox" id="auto-night-mode-time-range-goes-over-midgnight" data-setting-key="auto_night_mode_time_range_goes_over_midnight">
								<label class="form-check-label" for="auto-night-mode-time-range-goes-over-midgnight">
									<?php echo e($__t('Time range goes over midnight')); ?>

								</label>
							</div>
							<input class="form-check-input d-none user-setting-control" type="checkbox" id="currently-inside-night-mode-range" data-setting-key="currently_inside_night_mode_range">
						</div>
					</div>
				</li>
				<?php endif; ?>

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle discrete-link" href="#" data-toggle="dropdown"><i class="fas fa-wrench"></i> <span class="d-inline d-lg-none"><?php echo e($__t('Settings')); ?></span></a>

					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/stocksettings')); ?>"><i class="fas fa-box"></i>&nbsp;<?php echo e($__t('Stock settings')); ?></a>
						<?php if(GROCY_FEATURE_FLAG_CHORES): ?>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/choressettings')); ?>"><i class="fas fa-home"></i>&nbsp;<?php echo e($__t('Chores settings')); ?></a>
						<?php endif; ?>
						<?php if(GROCY_FEATURE_FLAG_BATTERIES): ?>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/batteriessettings')); ?>"><i class="fas fa-battery-half"></i>&nbsp;<?php echo e($__t('Batteries settings')); ?></a>
						<?php endif; ?>
						<?php if(GROCY_FEATURE_FLAG_TASKS): ?>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/taskssettings')); ?>"><i class="fas fa-tasks"></i>&nbsp;<?php echo e($__t('Tasks settings')); ?></a>
						<?php endif; ?>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/users')); ?>"><i class="fas fa-users"></i>&nbsp;<?php echo e($__t('Manage users')); ?></a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/manageapikeys')); ?>"><i class="fas fa-handshake"></i>&nbsp;<?php echo e($__t('Manage API keys')); ?></a>
						<a class="dropdown-item discrete-link" target="_blank" href="<?php echo e($U('/api')); ?>"><i class="fas fa-book"></i>&nbsp;<?php echo e($__t('REST API & data model documentation')); ?></a>
						<a class="dropdown-item discrete-link" href="<?php echo e($U('/barcodescannertesting')); ?>"><i class="fas fa-barcode"></i>&nbsp;<?php echo e($__t('Barcode scanner testing')); ?></a>
						<div class="dropdown-divider"></div>
						<a id="about-dialog-link" class="dropdown-item discrete-link" href="#"><i class="fas fa-info fa-fw"></i>&nbsp;<?php echo e($__t('About grocy')); ?> (Version <?php echo e($version); ?>)</a>
					</div>
				</li>
			</ul>
		</div><?php endif; ?>
	</nav>
	<?php endif; ?>

	<div class="content-wrapper">
		<div class="container-fluid">
			<div class="row mb-3">
				<div id="page-content" class="col content-text">
					<?php echo $__env->yieldContent('content'); ?>
				</div>
			</div>
		</div>
	</div>

	<script src="<?php echo e($U('/node_modules/jquery/dist/jquery.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/startbootstrap-sb-admin/js/sb-admin.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/bootbox/dist/bootbox.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/jquery-serializejson/jquery.serializejson.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/moment/min/moment.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<?php if(!empty($__t('moment_locale') && $__t('moment_locale') != 'x')): ?><script src="<?php echo e($U('/node_modules', true)); ?>/moment/locale/<?php echo e($__t('moment_locale')); ?>.js?v=<?php echo e($version); ?>"></script><?php endif; ?>
	<script src="<?php echo e($U('/node_modules/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net/js/jquery.dataTables.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-responsive/js/dataTables.responsive.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-colreorder/js/dataTables.colReorder.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-colreorder-bs4/js/colReorder.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-select/js/dataTables.select.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/datatables.net-select-bs4/js/select.bootstrap4.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/timeago/jquery.timeago.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules', true)); ?>/timeago/locales/jquery.timeago.<?php echo e($__t('timeago_locale')); ?>.js?v=<?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/toastr/build/toastr.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/sprintf-js/dist/sprintf.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/gettext-translator/src/translator.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/node_modules/summernote/dist/summernote-bs4.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<?php if(!empty($__t('summernote_locale') && $__t('summernote_locale') != 'x')): ?><script src="<?php echo e($U('/node_modules', true)); ?>/summernote/dist/lang/summernote-<?php echo e($__t('summernote_locale')); ?>.js?v=<?php echo e($version); ?>"></script><?php endif; ?>
	<script src="<?php echo e($U('/node_modules/bootstrap-select/dist/js/bootstrap-select.min.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<?php if(!empty($__t('bootstrap-select_locale') && $__t('bootstrap-select_locale') != 'x')): ?><script src="<?php echo e($U('/node_modules', true)); ?>/bootstrap-select/dist/js/i18n/defaults-<?php echo e($__t('bootstrap-select_locale')); ?>.js?v=<?php echo e($version); ?>"></script><?php endif; ?>
	<script src="<?php echo e($U('/node_modules/jquery-lazy/jquery.lazy.min.js?v=', true)); ?><?php echo e($version); ?>"></script>

	<script src="<?php echo e($U('/js/extensions.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/js/grocy.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/js/grocy_dbchangedhandling.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/js/grocy_nightmode.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<script src="<?php echo e($U('/js/grocy_clock.js?v=', true)); ?><?php echo e($version); ?>"></script>
	<?php echo $__env->yieldPushContent('pageScripts'); ?>
	<?php echo $__env->yieldPushContent('componentScripts'); ?>
	<?php if (! empty(trim($__env->yieldContent('viewJsName')))): ?><script src="<?php echo e($U('/viewjs', true)); ?>/<?php echo $__env->yieldContent('viewJsName'); ?>.js?v=<?php echo e($version); ?>"></script><?php endif; ?>

	<?php if(file_exists(GROCY_DATAPATH . '/custom_js.html')): ?>
		<?php include GROCY_DATAPATH . '/custom_js.html' ?>
	<?php endif; ?>
</body>

</html>
<?php /**PATH /www/views/layout/default.blade.php ENDPATH**/ ?>