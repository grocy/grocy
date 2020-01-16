@extends('layout.default')

@section('title', $__t('Stock entries'))
@section('activeNav', 'stockdetail')
@section('viewJsName', 'stockdetail')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<style>
		.product-name-cell[data-product-has-picture='true'] {
			cursor: pointer;
		}
	</style>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1> 
	</div>
	<div class="col">
		@include('components.productpicker', array('products' => $products,'disallowAddProductWorkflows' => true))
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-detail-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th class="d-none">product_id</th> <!-- This must be in the first column for searching -->
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th>{{ $__t('Best before date') }}</th>
					@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)<th>{{ $__t('Location') }}</th>@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)<th>{{ $__t('Price') }}</th>@endif
					<th>{{ $__t('Purchased date') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentStockDetail as $currentStockEntry)
				<tr id="stock-{{ $currentStockEntry->id }}-row" class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) table-danger @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0) table-warning @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) table-info @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm stock-consume-button @if($currentStockEntry->amount < 1) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Consume %1$s of %2$s', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-stock-id="{{ $currentStockEntry->stock_id }}"
							data-stockrow-id="{{ $currentStockEntry->id }}"
							data-location-id="{{ $currentStockEntry->location_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="stock-{{ $currentStockEntry->id }}-consume-all-button" class="btn btn-danger btn-sm stock-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $__t('Consume all %s for this stock entry', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-stock-id="{{ $currentStockEntry->stock_id }}"
							data-stockrow-id="{{ $currentStockEntry->id }}"
							data-location-id="{{ $currentStockEntry->location_id }}"
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
								<a class="dropdown-item product-add-to-shopping-list-button" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Add to shopping list') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-purchase-button" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Purchase') }}
								</a>
								<a class="dropdown-item product-consume-custom-amount-button @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-location-id="{{ $currentStockEntry->location_id }}"
									data-stock-id="{{ $currentStockEntry->stock_id }}">
									<i class="fas fa-utensils"></i> {{ $__t('Consume') }}
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item product-transfer-button @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
                                                                        data-location-id="{{ $currentStockEntry->location_id }}"
									data-stock-id="{{ $currentStockEntry->stock_id }}">
									<i class="fas fa-exchange-alt"></i> {{ $__t('Transfer') }}
								</a>
								@endif
								<a class="dropdown-item product-inventory-button" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<i class="fas fa-list"></i> {{ $__t('Inventory') }}
								</a>
								<a class="dropdown-item product-stockedit-button" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-location-id="{{ $currentStockEntry->location_id }}"
									data-id="{{ $currentStockEntry->id }}">
									<i class="fas fa-boxes"></i> {{ $__t('Edit stock entry') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell" data-product-id="{{ $currentStockEntry->product_id }}" type="button" href="#">
									<i class="fas fa-info"></i> {{ $__t('Show product details') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockjournal?product=') }}{{ $currentStockEntry->product_id }}">
									<i class="fas fa-file-alt"></i> {{ $__t('Stock journal for this product') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/product/') }}{{ $currentStockEntry->product_id . '?returnto=%2Fstockdetail' }}">
									<i class="fas fa-edit"></i> {{ $__t('Edit product') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item stock-consume-button stock-consume-button-spoiled @if($currentStockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $currentStockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
									data-stock-id="{{ $currentStockEntry->stock_id }}"
									data-stockrow-id="{{ $currentStockEntry->id }}"
									data-location-id="{{ $currentStockEntry->location_id }}"
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
					<td class="d-none" data-product-id="{{ $currentStockEntry->product_id }}">
						{{ $currentStockEntry->product_id }}
					</td>
					<td class="product-name-cell cursor-link" data-product-id="{{ $currentStockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
					</td>
					<td>
						<span id="stock-{{ $currentStockEntry->id }}-amount" class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
						<span id="stock-{{ $currentStockEntry->id }}-opened-amount" class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						@if($currentStockEntry->is_aggregated_amount == 1)
						<span class="pl-1 text-secondary">
							<i class="fas fa-custom-sigma-sign"></i> <span id="product-{{ $currentStockEntry->product_id }}-amount-aggregated" class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount_aggregated }}</span> {{ $__n($currentStockEntry->amount_aggregated, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}
							@if($currentStockEntry->amount_opened_aggregated > 0)<span id="product-{{ $currentStockEntry->product_id }}-opened-amount-aggregated" class="small font-italic">{{ $__t('%s opened', $currentStockEntry->amount_opened_aggregated) }}</span>@endif
						</span>
						@endif
					</td>
					<td>
						<span id="stock-{{ $currentStockEntry->id }}-best-before-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="stock-{{ $currentStockEntry->id }}-best-before-date-timeago" class="timeago timeago-contextual" datetime="{{ $currentStockEntry->best_before_date }} 23:59:59"></time>
					</td>
					@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					<td id="stock-{{ $currentStockEntry->id }}-location" class="location-name-cell cursor-link" data-location-id="{{ $currentStockEntry->location_id }}">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $currentStockEntry->location_id)->name }}
					</td>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<td id="stock-{{ $currentStockEntry->id }}-price" class="price-name-cell cursor-link" data-price-id="{{ $currentStockEntry->price }}">
						{{ $currentStockEntry->price }}
					</td>
					@endif
					<td>
						<span id="stock-{{ $currentStockEntry->id }}-purchased-date">{{ $currentStockEntry->purchased_date }}</span>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="stockdetail-productcard-modal" tabindex="-1">
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
