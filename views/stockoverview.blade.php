@extends('layout.default')

@section('title', $__t('Stock overview'))
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@push('pageScripts')
	<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')
			<small id="info-current-stock" class="text-muted"></small>
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/stockjournal') }}">
				<i class="fas fa-file-alt"></i> {{ $__t('Journal') }}
			</a>
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/stockentries') }}">
				<i class="fas fa-boxes"></i> {{ $__t('Stock entries') }}
			</a>
			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/locationcontentsheet') }}">
				<i class="fas fa-print"></i> {{ $__t('Location Content Sheet') }}
			</a>
			@endif
		</h1>
		@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		<p id="info-expiring-products" data-next-x-days="{{ $nextXDays }}" data-status-filter="expiring" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-expired-products" data-status-filter="expired" class="btn btn-lg btn-danger status-filter-button responsive-button mr-2"></p>
		@endif
		<p id="info-missing-products" data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter">{{ $__t('Filter by location') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="location-filter">
			<option value="all">{{ $__t('All') }}</option>
			@foreach($locations as $location)
				<option value="{{ $location->name }}">{{ $location->name }}</option>
			@endforeach
		</select>
	</div>
	@endif
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter">{{ $__t('Filter by product group') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="product-group-filter">
			<option value="all">{{ $__t('All') }}</option>
			@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $__t('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $__t('All') }}</option>
			@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<option class="bg-warning" value="expiring">{{ $__t('Expiring soon') }}</option>
			<option class="bg-danger" value="expired">{{ $__t('Already expired') }}</option>
			@endif
			<option class="bg-info" value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th>{{ $__t('Next best before date') }}</th>
					<th class="d-none">Hidden location</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden product group</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
					
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentStock as $currentStockEntry) 
				<tr id="product-{{ $currentStockEntry->product_id }}-row" class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) table-danger @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0) table-warning @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) table-info @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount < 1) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Consume %1$s of %2$s', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button" class="btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $__t('Consume all %s which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="{{ $currentStockEntry->amount }}">
							<i class="fas fa-utensils"></i> {{ $__t('All') }}
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount < 1 || $currentStockEntry->amount == $currentStockEntry->amount_opened) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Mark %1$s of %2$s as open', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}">
							<i class="fas fa-box-open"></i> 1
						</a>
						@endif
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=' . $currentStockEntry->product_id ) }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Add to shopping list') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/purchase?embedded&product=' . $currentStockEntry->product_id ) }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Purchase') }}
								</a>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/consume?embedded&product=' . $currentStockEntry->product_id ) }}">
									<i class="fas fa-utensils"></i> {{ $__t('Consume') }}
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="{{ $U('/transfer?embedded&product=' . $currentStockEntry->product_id) }}">
									<i class="fas fa-exchange-alt"></i> {{ $__t('Transfer') }}
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/inventory?embedded&product=' . $currentStockEntry->product_id ) }}">
									<i class="fas fa-list"></i> {{ $__t('Inventory') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell" data-product-id="{{ $currentStockEntry->product_id }}" type="button" href="#">
									<i class="fas fa-info"></i> {{ $__t('Show product details') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockentries?product=') }}{{ $currentStockEntry->product_id }}"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<i class="fas fa-boxes"></i> {{ $__t('Show stock entries') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockjournal?product=') }}{{ $currentStockEntry->product_id }}">
									<i class="fas fa-file-alt"></i> {{ $__t('Stock journal for this product') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/product/') }}{{ $currentStockEntry->product_id . '?returnto=%2Fstockoverview' }}">
									<i class="fas fa-edit"></i> {{ $__t('Edit product') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-consume-button product-consume-button-spoiled @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
									data-consume-amount="1">
									<i class="fas fa-utensils"></i> {{ $__t('Consume %1$s of %2$s as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<a class="dropdown-item" type="button" href="{{ $U('/recipes?search=') }}{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}">
									<i class="fas fa-cocktail"></i> {{ $__t('Search for recipes containing this product') }}
								</a>
								@endif
							</div>
						</div>
					</td>
					<td class="product-name-cell cursor-link" data-product-id="{{ $currentStockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-amount" class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
						<span id="product-{{ $currentStockEntry->product_id }}-opened-amount" class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						@if($currentStockEntry->is_aggregated_amount == 1)
						<span class="pl-1 text-secondary">
							<i class="fas fa-custom-sigma-sign"></i> <span id="product-{{ $currentStockEntry->product_id }}-amount-aggregated" class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount_aggregated }}</span> {{ $__n($currentStockEntry->amount_aggregated, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}
							@if($currentStockEntry->amount_opened_aggregated > 0)<span id="product-{{ $currentStockEntry->product_id }}-opened-amount-aggregated" class="small font-italic">{{ $__t('%s opened', $currentStockEntry->amount_opened_aggregated) }}</span>@endif
						</span>
						@endif
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-next-best-before-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="product-{{ $currentStockEntry->product_id }}-next-best-before-date-timeago" class="timeago timeago-contextual" datetime="{{ $currentStockEntry->best_before_date }} 23:59:59"></time>
					</td>
					<td class="d-none">
						@foreach(FindAllObjectsInArrayByPropertyValue($currentStockLocations, 'product_id', $currentStockEntry->product_id) as $locationsForProduct) 
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $locationsForProduct->location_id)->name }}
						@endforeach
					</td>
					<td class="d-none">
						@if($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) expired @elseif($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0) expiring @endif @if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) belowminstockamount @endif
					</td>
					@php $productGroup = FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->product_group_id) @endphp
					<td class="d-none">
						@if($productGroup !== null){{ $productGroup->name }}@endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentStockEntry->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="stockoverview-productcard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.productcard')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
