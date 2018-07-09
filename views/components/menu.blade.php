<ul class="list-group list-group-flush sidebar-nav">
	<a class="discrete-link" href="{{ $U('/stockoverview') }}">
		<li class="list-group-item" data-nav-for-page="stockoverview">
			<i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Stock overview') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/habitsoverview') }}">
		<li class="list-group-item" data-nav-for-page="habitsoverview">
			<i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Habits overview') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/batteriesoverview') }}">
		<li class="list-group-item" data-nav-for-page="batteriesoverview">
			<i class="fa fa-tachometer fa-fw"></i>&nbsp;{{ $L('Batteries overview') }}
		</li>
	</a>
</ul>

<div class="discrete-content-separator-2x"></div>

<ul class="list-group list-group-flush sidebar-nav">
	<li class="list-group-item disabled"><a href="#"><strong>{{ $L('Record data') }}</strong></a></li>
	<a class="discrete-link" href="{{ $U('/purchase') }}">
		<li class="list-group-item" data-nav-for-page="purchase">
			<i class="fa fa-shopping-cart fa-fw"></i>&nbsp;{{ $L('Purchase') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/consume') }}">
		<li class="list-group-item" data-nav-for-page="consume">
			<i class="fa fa-cutlery fa-fw"></i>&nbsp;{{ $L('Consume') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/shoppinglist') }}">
		<li class="list-group-item" data-nav-for-page="shoppinglist">
			<i class="fa fa-shopping-bag fa-fw"></i>&nbsp;{{ $L('Shopping list') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/inventory') }}">
		<li class="list-group-item" data-nav-for-page="inventory">
			<i class="fa fa-list fa-fw"></i>&nbsp;{{ $L('Inventory') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/habittracking') }}">
		<li class="list-group-item" data-nav-for-page="habittracking">
			<i class="fa fa-play fa-fw"></i>&nbsp;{{ $L('Habit tracking') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/batterytracking') }}">
		<li class="list-group-item" data-nav-for-page="batterytracking">
			<i class="fa fa-fire fa-fw"></i>&nbsp;{{ $L('Battery tracking') }}
		</li>
	</a>
</ul>

<div class="discrete-content-separator-2x"></div>

<ul class="list-group list-group-flush sidebar-nav">
	<li class="list-group-item disabled"><a href="#"><strong>{{ $L('Manage master data') }}</strong></a></li>
	
	<a class="discrete-link" href="{{ $U('/products') }}">
		<li class="list-group-item" data-nav-for-page="products">
			<i class="fa fa-product-hunt fa-fw"></i>&nbsp;{{ $L('Products') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/locations') }}">
		<li class="list-group-item" data-nav-for-page="locations">
			<i class="fa fa-map-marker fa-fw"></i>&nbsp;{{ $L('Locations') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/quantityunits') }}">
		<li class="list-group-item" data-nav-for-page="quantityunits">
			<i class="fa fa-balance-scale fa-fw"></i>&nbsp;{{ $L('Quantity units') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/habits') }}">
		<li class="list-group-item" data-nav-for-page="habits">
			<i class="fa fa-refresh fa-fw"></i>&nbsp;{{ $L('Habits') }}
		</li>
	</a>
	<a class="discrete-link" href="{{ $U('/batteries') }}">
		<li class="list-group-item" data-nav-for-page="batteries">
			<i class="fa fa-battery-three-quarters fa-fw"></i>&nbsp;{{ $L('Batteries') }}
		</li>
	</a>
</ul>

<div class="discrete-content-separator-2x hidden-xs"></div>

<ul class="list-group list-group-flush nav-copyright">
	<a class="discrete-link" href="#" data-toggle="modal" data-target="#about-modal">
		<li class="list-group-item">
			Version {{ $version }}<br>
			{{ $L('About grocy') }}
		</li>
	</a>
</ul>
