<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)">
	<link rel="icon" type="image/png" sizes="200x200" href="/grocy.png">

	<title><?php echo $title; ?> | grocy</title>

	<link href="/bower_components/bootstrap/dist/css/bootstrap.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/font-awesome/css/font-awesome.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/bootstrap-combobox/css/bootstrap-combobox.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/toastr/toastr.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/tagmanager/tagmanager.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/vendor_unmanaged/noto-sans-v6-latin/noto-sans-v6-latin.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">
	<link href="/style.css?v=<?php echo Grocy::GetInstalledVersion(); ?>" rel="stylesheet">

	<script src="/bower_components/jquery/dist/jquery.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/grocy.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-mobile" >
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">grocy</a>
			</div>

			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a class="discrete-link logout-button" href="/logout"><i class="fa fa-sign-out fa-fw"></i>&nbsp;Logout</a>
					</li>
				</ul>
			</div>

			<div id="navbar-mobile" class="navbar-collapse collapse">

				<ul class="nav navbar-nav navbar-right">
					<li data-nav-for-page="stockoverview.php">
						<a class="discrete-link" href="/stockoverview"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Stock overview</a>
					</li>
					<li data-nav-for-page="habitsoverview.php">
						<a class="discrete-link" href="/habitsoverview"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Habits overview</a>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li data-nav-for-page="purchase.php">
						<a class="discrete-link" href="/purchase"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;Purchase</a>
					</li>
					<li data-nav-for-page="consume.php">
						<a class="discrete-link" href="/consume"><i class="fa fa-cutlery fa-fw"></i>&nbsp;Consume</a>
					</li>
					<li data-nav-for-page="inventory.php">
						<a class="discrete-link" href="/inventory"><i class="fa fa-list fa-fw"></i>&nbsp;Inventory</a>
					</li>
					<li data-nav-for-page="shoppinglist.php">
						<a class="discrete-link" href="/shoppinglist"><i class="fa fa-shopping-bag fa-fw"></i>&nbsp;Shopping list</a>
					</li>
					<li data-nav-for-page="habittracking.php">
						<a class="discrete-link" href="/habittracking"><i class="fa fa-play fa-fw"></i>&nbsp;Habit tracking</a>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li class="disabled"><a href="#"><strong>Manage master data</strong></a></li>
					<li data-nav-for-page="products.php">
						<a class="discrete-link" href="/products"><i class="fa fa-product-hunt fa-fw"></i>&nbsp;Products</a>
					</li>
					<li data-nav-for-page="locations.php">
						<a class="discrete-link" href="/locations"><i class="fa fa-map-marker fa-fw"></i>&nbsp;Locations</a>
					</li>
					<li data-nav-for-page="quantityunits.php">
						<a class="discrete-link" href="/quantityunits"><i class="fa fa-balance-scale fa-fw"></i>&nbsp;Quantity units</a>
					</li>
					<li data-nav-for-page="habits.php">
						<a class="discrete-link" href="/habits"><i class="fa fa-refresh fa-fw"></i>&nbsp;Habits</a>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li>
						<a class="discrete-link logout-button" href="/logout"><i class="fa fa-sign-out fa-fw"></i>&nbsp;Logout</a>
					</li>
				</ul>

			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">

			<div class="col-sm-3 col-md-2 sidebar">

				<ul class="nav nav-sidebar">
					<li data-nav-for-page="stockoverview.php">
						<a class="discrete-link" href="/stockoverview"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Stock overview</a>
					</li>
					<li data-nav-for-page="habitsoverview.php">
						<a class="discrete-link" href="/habitsoverview"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Habits overview</a>
					</li>
				</ul>

				<ul class="nav nav-sidebar">
					<li data-nav-for-page="purchase.php">
						<a class="discrete-link" href="/purchase"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;Purchase</a>
					</li>
					<li data-nav-for-page="consume.php">
						<a class="discrete-link" href="/consume"><i class="fa fa-cutlery fa-fw"></i>&nbsp;Consume</a>
					</li>
					<li data-nav-for-page="inventory.php">
						<a class="discrete-link" href="/inventory"><i class="fa fa-list fa-fw"></i>&nbsp;Inventory</a>
					</li>
					<li data-nav-for-page="shoppinglist.php">
						<a class="discrete-link" href="/shoppinglist"><i class="fa fa-shopping-bag fa-fw"></i>&nbsp;Shopping list</a>
					</li>
					<li data-nav-for-page="habittracking.php">
						<a class="discrete-link" href="/habittracking"><i class="fa fa-play fa-fw"></i>&nbsp;Habit tracking</a>
					</li>
				</ul>

				<ul class="nav nav-sidebar">
					<li class="disabled"><a href="#"><strong>Manage master data</strong></a></li>
					<li data-nav-for-page="products.php">
						<a class="discrete-link" href="/products"><i class="fa fa-product-hunt fa-fw"></i>&nbsp;Products</a>
					</li>
					<li data-nav-for-page="locations.php">
						<a class="discrete-link" href="/locations"><i class="fa fa-map-marker fa-fw"></i>&nbsp;Locations</a>
					</li>
					<li data-nav-for-page="quantityunits.php">
						<a class="discrete-link" href="/quantityunits"><i class="fa fa-balance-scale fa-fw"></i>&nbsp;Quantity units</a>
					</li>
					<li data-nav-for-page="habits.php">
						<a class="discrete-link" href="/habits"><i class="fa fa-refresh fa-fw"></i>&nbsp;Habits</a>
					</li>
				</ul>

				<div class="nav-copyright nav nav-sidebar">
					grocy is a project by
					<a class="discrete-link" href="https://berrnd.de" target="_blank">Bernd Bestel</a>
					<br>
					Created with passion since 2017
					<br>
					Version <?php echo Grocy::GetInstalledVersion(); ?>
					<br>
					Life runs on code
					<br>
					<a class="discrete-link" href="https://github.com/berrnd/grocy" target="_blank">
						<i class="fa fa-github"></i>
					</a>
				</div>

			</div>

			<script>Grocy.ContentPage = '<?php echo $contentPage; ?>';</script>
			<?php include $contentPage; ?>

		</div>
	</div>

	<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/bootbox/bootbox.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/jquery.serializeJSON/jquery.serializejson.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/moment/min/moment.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/bootstrap-validator/dist/validator.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/bootstrap-combobox/js/bootstrap-combobox.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/datatables.net/js/jquery.dataTables.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/jquery-timeago/jquery.timeago.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/toastr/toastr.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/tagmanager/tagmanager.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js?v=<?php echo Grocy::GetInstalledVersion(); ?>"></script>

	<?php if (file_exists(__DIR__ . '/' . str_replace('.php', '.js', $contentPage))) : ?>
		<script src="/views/<?php echo str_replace('.php', '.js', $contentPage) . '?v=' . Grocy::GetInstalledVersion(); ?>"></script>
	<?php endif; ?>

	<?php if (file_exists(__DIR__ . '/../data/add_before_end_body.html')) include __DIR__ . '/../data/add_before_end_body.html' ?>
</body>
</html>
