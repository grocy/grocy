
<ul class="nav flex-column sidebar-nav">
	<li class="nav-item" data-nav-for-page="stockoverview">
		<a class="discrete-link" href="{{ $U('/stockoverview') }}"><i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Stock overview') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="habitsoverview">
		<a class="discrete-link" href="{{ $U('/habitsoverview') }}"><i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Habits overview') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="batteriesoverview">
		<a class="discrete-link" href="{{ $U('/batteriesoverview') }}"><i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Batteries overview') }}</a>
	</li>
</ul>

<div class="discrete-content-separator-2x"></div>

<ul class="nav flex-column">
	<li class="nav-item" class="disabled"><a href="#"><strong>{{ $L('Record data') }}</strong></a></li>
	<li class="nav-item" data-nav-for-page="purchase">
		<a class="discrete-link" href="{{ $U('/purchase') }}"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;{{ $L('Purchase') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="consume">
		<a class="discrete-link" href="{{ $U('/consume') }}"><i class="fa fa-cutlery fa-fw"></i>&nbsp;{{ $L('Consume') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="shoppinglist">
		<a class="discrete-link" href="{{ $U('/shoppinglist') }}"><i class="fa fa-shopping-bag fa-fw"></i>&nbsp;{{ $L('Shopping list') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="inventory">
		<a class="discrete-link" href="{{ $U('/inventory') }}"><i class="fa fa-list fa-fw"></i>&nbsp;{{ $L('Inventory') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="habittracking">
		<a class="discrete-link" href="{{ $U('/habittracking') }}"><i class="fa fa-play fa-fw"></i>&nbsp;{{ $L('Habit tracking') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="batterytracking">
		<a class="discrete-link" href="{{ $U('/batterytracking') }}"><i class="fa fa-fire fa-fw"></i>&nbsp;{{ $L('Battery tracking') }}</a>
	</li>
</ul>

<div class="discrete-content-separator-2x"></div>

<ul class="nav flex-column">
	<li class="nav-item" class="disabled"><a href="#"><strong>{{ $L('Manage master data') }}</strong></a></li>
	<li class="nav-item" data-nav-for-page="products">
		<a class="discrete-link" href="{{ $U('/products') }}"><i class="fa fa-product-hunt fa-fw"></i>&nbsp;{{ $L('Products') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="locations">
		<a class="discrete-link" href="{{ $U('/locations') }}"><i class="fa fa-map-marker fa-fw"></i>&nbsp;{{ $L('Locations') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="quantityunits">
		<a class="discrete-link" href="{{ $U('/quantityunits') }}"><i class="fa fa-balance-scale fa-fw"></i>&nbsp;{{ $L('Quantity units') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="habits">
		<a class="discrete-link" href="{{ $U('/habits') }}"><i class="fa fa-refresh fa-fw"></i>&nbsp;{{ $L('Habits') }}</a>
	</li>
	<li class="nav-item" data-nav-for-page="batteries">
		<a class="discrete-link" href="{{ $U('/batteries') }}"><i class="fa fa-battery-three-quarters fa-fw"></i>&nbsp;{{ $L('Batteries') }}</a>
	</li>
</ul>

<div class="discrete-content-separator-2x hidden-xs"></div>

<ul class="nav flex-column nav-copyright">
	<li class="nav-item">
		Version {{ $version }}<br>
		<a class="discrete-link" href="#" data-toggle="modal" data-target="#about-modal">{{ $L('About grocy') }}</a>
	</li>
</ul>
