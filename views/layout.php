<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<meta name="robots" content="noindex,nofollow" />

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)" />

	<title>
		<?php echo $title; ?> | grocy
	</title>

	<link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet" />
	<link href="/bower_components/bootstrap-combobox/css/bootstrap-combobox.css" rel="stylesheet" />
	<link href="/style.css" rel="stylesheet" />

	<script src="/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="/grocy.js"></script>
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<!--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" >
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>-->
				<a class="navbar-brand" href="/">grocy</a>
			</div>

			<div id="navbar" class="navbar-collapse collapse">
				<!--<ul class="nav navbar-nav navbar-right">
					<li>
						<a href="#">About</a>
					</li>
				</ul>-->
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3 col-md-2 sidebar">
				<ul class="nav nav-sidebar">
					<li data-nav-for-page="dashboard.php">
						<a class="discrete-link" href="/"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Dashboard</a>
					</li>
					<li data-nav-for-page="purchase.php">
						<a class="discrete-link" href="/purchase"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;Record purchase</a>
					</li>
					<li data-nav-for-page="consumption.php">
						<a class="discrete-link" href="/consumption"><i class="fa fa-cutlery fa-fw"></i>&nbsp;Record consumption</a>
					</li>
				</ul>
				<ul class="nav nav-sidebar">
					<li data-nav-for-page="products.php">
						<a class="discrete-link" href="/products"><i class="fa fa-product-hunt fa-fw"></i>&nbsp;Manage products</a>
					</li>
					<li data-nav-for-page="locations.php">
						<a class="discrete-link" href="/locations"><i class="fa fa-map-marker fa-fw"></i>&nbsp;Manage locations</a>
					</li>
					<li data-nav-for-page="quantityunits.php">
						<a class="discrete-link" href="/quantityunits"><i class="fa fa-balance-scale fa-fw"></i>&nbsp;Manage quantity units</a>
					</li>
				</ul>
				<div class="nav-copyright nav nav-sidebar">
					grocy is a project by
					<a class="discrete-link" href="https://berrnd.de" target="_blank">Bernd Bestel</a>
					<br />
					Created with passion since 2017
					<br />
					Version <?php echo file_get_contents('version.txt'); ?>
					<br />
					Life runs on code
					<br />
					<a class="discrete-link" href="https://github.com/berrnd/grocy" target="_blank">
						<i class="fa fa-github"></i>
					</a>
				</div>
			</div>

			<script>Grocy.ContentPage = '<?php echo $contentPage; ?>';</script>
			<?php include $contentPage; ?>
		</div>
	</div>

	<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="/bower_components/bootbox/bootbox.js"></script>
	<script src="/bower_components/jquery.serializeJSON/jquery.serializejson.min.js"></script>
	<script src="/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
	<script src="/bower_components/moment/min/moment.min.js"></script>
	<script src="/bower_components/bootstrap-validator/dist/validator.min.js"></script>
	<script src="/bower_components/bootstrap-combobox/js/bootstrap-combobox.js"></script>

	<?php if (file_exists('views/' . str_replace('.php', '.js', $contentPage))) : ?>
		<script src="/views/<?php echo str_replace('.php', '.js', $contentPage); ?>"></script>
	<?php endif; ?>

	<?php if (file_exists('data/add_before_end_body.html')) include 'data/add_before_end_body.html' ?>
</body>
</html>
