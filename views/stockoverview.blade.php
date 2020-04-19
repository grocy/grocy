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
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-dark responsive-button" href="{{ $U('/stockjournal') }}">
					{{ $__t('Journal') }}
				</a>
				<a class="btn btn-outline-dark responsive-button" href="{{ $U('/stockentries') }}">
					{{ $__t('Stock entries') }}
				</a>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				<a class="btn btn-outline-dark responsive-button" href="{{ $U('/locationcontentsheet') }}">
					{{ $__t('Location Content Sheet') }}
				</a>
				@endif
			</div>
		</div>
		<div id="info-current-stock" class="text-muted"></div>
		<hr>
		@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		<p id="info-expiring-products" data-next-x-days="{{ $nextXDays }}" data-status-filter="expiring" class="warning-message status-filter-message responsive-button mr-2"></p>
		<p id="info-expired-products" data-status-filter="expired" class="error-message status-filter-message responsive-button mr-2"></p>
		@endif
		<p id="info-missing-products" data-status-filter="belowminstockamount" class="normal-message status-filter-message responsive-button"></p>
	</div>
</div>
<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3 d-flex align-items-end">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter">{{ $__t('Location') }}</label>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
					<option value="{{ $location->name }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="product-group-filter">{{ $__t('Product group') }}</label>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
					<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $__t('Status') }}</label>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="status-filter">
				<option class="bg-white" value="all">{{ $__t('All') }}</option>
				@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				<option value="expiring">{{ $__t('Expiring soon') }}</option>
				<option value="expired">{{ $__t('Already expired') }}</option>
				@endif
				<option value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
			</select>
		</div>
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
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">{{ $__t('Next best before date') }}</th>
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
						<a class="btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount < 1 || FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->enable_tare_weight_handling == 1) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Consume %1$s of %2$s', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button" class="d-none d-sm-inline-block btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $__t('Consume all %s which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="@if(FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->enable_tare_weight_handling == 1){{FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->tare_weight}}@else{{$currentStockEntry->amount}}@endif"
							data-original-total-stock-amount="{{$currentStockEntry->amount}}">
							<i class="fas fa-utensils"></i> {{ $__t('All') }}
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount < 1 || $currentStockEntry->amount == $currentStockEntry->amount_opened || FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->enable_tare_weight_handling == 1) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Mark %1$s of %2$s as open', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
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
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button" class="d-inline-block d-sm-none dropdown-item show-as-dialog-link text-danger product-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="right"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
									data-consume-amount="{{ $currentStockEntry->amount }}">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume all %s which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Add to shopping list') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/purchase?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Purchase') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/consume?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume') }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="{{ $U('/transfer?embedded&product=' . $currentStockEntry->product_id) }}">
									<span class="dropdown-item-icon"><i class="fas fa-exchange-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Transfer') }}</span>
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/inventory?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fas fa-list"></i></span> <span class="dropdown-item-text">{{ $__t('Inventory') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell" data-product-id="{{ $currentStockEntry->product_id }}" type="button" href="#">
									<span class="dropdown-item-icon"><i class="fas fa-info"></i></span> <span class="dropdown-item-text">{{ $__t('Show product details') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockentries?product=') }}{{ $currentStockEntry->product_id }}"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-boxes"></i></span> <span class="dropdown-item-text">{{ $__t('Show stock entries') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockjournal?product=') }}{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-file-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Stock journal for this product') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/product/') }}{{ $currentStockEntry->product_id . '?returnto=%2Fstockoverview' }}">
									<span class="dropdown-item-icon"><i class="fas fa-edit"></i></span> <span class="dropdown-item-text">{{ $__t('Edit product') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-consume-button product-consume-button-spoiled @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
									data-consume-amount="1">
									<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume %1$s of %2$s as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<a class="dropdown-item" type="button" href="{{ $U('/recipes?search=') }}{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}">
									<span class="dropdown-item-icon"><i class="fas fa-cocktail"></i></span> <span class="dropdown-item-text">{{ $__t('Search for recipes containing this product') }}</span>
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
						@if(boolval($userSettings['show_icon_on_stock_overview_page_when_product_is_on_shopping_list']))
						@php $currentStockEntryShoppingListItems = FindAllObjectsInArrayByPropertyValue($shoppingListItems, 'product_id', $currentStockEntry->product_id) @endphp
						@if(count($currentStockEntryShoppingListItems) > 0)
						<span class="btn btn-link btn-sm text-muted">
							<i class="fas fa-shopping-cart"></i>
						</span>
						@endif
						@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">
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
