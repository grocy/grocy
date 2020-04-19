@extends('layout.default')

@section('title', $__t('Stock entries'))
@section('viewJsName', 'stockentries')

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@push('pageScripts')
	<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2> 
	</div>
	<div class="col">
		@include('components.productpicker', array(
			'products' => $products,
			'disallowAllProductWorkflows' => true
		))
	</div>
</div>
<hr>
<div class="row">
	<div class="col">
		<table id="stockentries-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th class="d-none">product_id</th> <!-- This must be in the first column for searching -->
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					<th>{{ $__t('Best before date') }}</th>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)<th>{{ $__t('Location') }}</th>@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<th>{{ $__t('Store') }}</th>
					<th>{{ $__t('Price') }}</th>
					@endif
					<th>{{ $__t('Purchased date') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($stockEntries as $stockEntry)
				<tr id="stock-{{ $stockEntry->id }}-row" class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $stockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $stockEntry->amount > 0) table-danger @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $stockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $stockEntry->amount > 0) table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-danger btn-sm stock-consume-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Consume this stock entry') }}"
							data-product-id="{{ $stockEntry->product_id }}"
							data-stock-id="{{ $stockEntry->stock_id }}"
							data-stockrow-id="{{ $stockEntry->id }}"
							data-location-id="{{ $stockEntry->location_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="{{ $stockEntry->amount }}">
							<i class="fas fa-utensils"></i>
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($stockEntry->open == 1) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Mark this stock entry as open') }}"
							data-product-id="{{ $stockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
							data-stock-id="{{ $stockEntry->stock_id }}"
							data-stockrow-id="{{ $stockEntry->id }}">
							<i class="fas fa-box-open"></i>
						</a>
						@endif
						<a class="btn btn-info btn-sm show-as-dialog-link" href="{{ $U('/stockentry/' . $stockEntry->id . '?embedded') }}" data-toggle="tooltip" data-placement="left" title="{{ $__t('Edit stock entry') }}">
							<i class="fas fa-edit"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Add to shopping list') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/purchase?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Purchase') }}
								</a>
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/consume?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fas fa-utensils"></i> {{ $__t('Consume') }}
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
									<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/transfer?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fas fa-exchange-alt"></i> {{ $__t('Transfer') }}
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link" type="button" href="{{ $U('/inventory?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-list"></i> {{ $__t('Inventory') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell" data-product-id="{{ $stockEntry->product_id }}" type="button" href="#">
									<i class="fas fa-info"></i> {{ $__t('Show product details') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/stockjournal?product=') }}{{ $stockEntry->product_id }}">
									<i class="fas fa-file-alt"></i> {{ $__t('Stock journal for this product') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/product/') }}{{ $stockEntry->product_id . '?returnto=/stockentries' }}">
									<i class="fas fa-edit"></i> {{ $__t('Edit product') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item stock-consume-button stock-consume-button-spoiled @if($stockEntry->amount < 1) disabled @endif" type="button" href="#"
									data-product-id="{{ $stockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
									data-stock-id="{{ $stockEntry->stock_id }}"
									data-stockrow-id="{{ $stockEntry->id }}"
									data-location-id="{{ $stockEntry->location_id }}"
									data-consume-amount="1">
									<i class="fas fa-utensils"></i> {{ $__t('Consume %1$s of %2$s as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name) }}
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<a class="dropdown-item" type="button" href="{{ $U('/recipes?search=') }}{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}">
									<i class="fas fa-cocktail"></i> {{ $__t('Search for recipes containing this product') }}
								</a>
								@endif
							</div>
						</div>
					</td>
					<td class="d-none" data-product-id="{{ $stockEntry->product_id }}">
						{{ $stockEntry->product_id }}
					</td>
					<td class="product-name-cell cursor-link" data-product-id="{{ $stockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}
					</td>
					<td>
						<span id="stock-{{ $stockEntry->id }}-amount" class="locale-number locale-number-quantity-amount">{{ $stockEntry->amount }}</span> <span id="product-{{ $stockEntry->product_id }}-qu-name">{{ $__n($stockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
						<span id="stock-{{ $stockEntry->id }}-opened-amount" class="small font-italic">@if($stockEntry->open == 1){{ $__t('Opened') }}@endif</span>
					</td>
					@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					<td>
						<span id="stock-{{ $stockEntry->id }}-best-before-date">{{ $stockEntry->best_before_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-best-before-date-timeago" class="timeago timeago-contextual" datetime="{{ $stockEntry->best_before_date }} 23:59:59"></time>
					</td>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					<td id="stock-{{ $stockEntry->id }}-location" data-location-id="{{ $stockEntry->location_id }}">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $stockEntry->location_id)->name }}
					</td>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<td id="stock-{{ $stockEntry->id }}-shopping-location" data-shopping-location-id="{{ $stockEntry->shopping_location_id }}">
						@if (FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id) !== null)
						{{ FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id)->name }}
						@endif
					</td>
					<td id="stock-{{ $stockEntry->id }}-price" class="locale-number locale-number-currency" data-price-id="{{ $stockEntry->price }}">
						{{ $stockEntry->price }}
					</td>
					@endif
					<td>
						<span id="stock-{{ $stockEntry->id }}-purchased-date">{{ $stockEntry->purchased_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-purchased-date-timeago" class="timeago timeago-contextual" datetime="{{ $stockEntry->purchased_date }} 23:59:59"></time>
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $stockEntry->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="productcard-modal" tabindex="-1">
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
