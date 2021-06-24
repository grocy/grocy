@extends($rootLayout)

@section('title', $__t('Stock entries'))
@section('viewJsName', 'stockentries')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right">
			<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fas fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-xs-12 col-md-6 col-xl-3">
		@include('components.productpicker', array(
		'products' => $products,
		'disallowAllProductWorkflows' => true,
		'isRequired' => false
		))
	</div>
	<div class="col">
		<div class="float-right mt-3">
			<a id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				href="#">
				{{ $__t('Clear filter') }}
			</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stockentries-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#stockentries-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th class="d-none">product_id</th> <!-- This must be in the first column for searching -->
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					<th>{{ $__t('Due date') }}</th>
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
				<tr id="stock-{{ $stockEntry->id }}-row"
					data-due-type="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->due_type }}"
					class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $stockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $stockEntry->amount > 0) @if(FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->due_type == 1) table-secondary @else table-danger @endif @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $stockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('+' . $nextXDays . ' days'))
					&&
					$stockEntry->amount > 0) table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-danger btn-sm stock-consume-button"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Consume this stock entry') }}"
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
						<a class="btn btn-success btn-sm product-open-button @if($stockEntry->open == 1 || FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->enable_tare_weight_handling == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark this stock entry as open') }}"
							data-product-id="{{ $stockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
							data-stock-id="{{ $stockEntry->stock_id }}"
							data-stockrow-id="{{ $stockEntry->id }}">
							<i class="fas fa-box-open"></i>
						</a>
						@endif
						<a class="btn btn-info btn-sm show-as-dialog-link"
							href="{{ $U('/stockentry/' . $stockEntry->id . '?embedded') }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Edit stock entry') }}">
							<i class="fas fa-edit"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-shopping-cart"></i> {{ $__t('Add to shopping list') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/purchase?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-cart-plus"></i> {{ $__t('Purchase') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/consume?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fas fa-utensils"></i> {{ $__t('Consume') }}
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/transfer?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fas fa-exchange-alt"></i> {{ $__t('Transfer') }}
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/inventory?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fas fa-list"></i> {{ $__t('Inventory') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item stock-consume-button stock-consume-button-spoiled @if($stockEntry->amount < 1) disabled @endif"
									type="button"
									href="#"
									data-product-id="{{ $stockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
									data-stock-id="{{ $stockEntry->stock_id }}"
									data-stockrow-id="{{ $stockEntry->id }}"
									data-location-id="{{ $stockEntry->location_id }}"
									data-consume-amount="1">
									{{ $__t('Consume this stock entry as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name) }}
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/recipes?search=') }}{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}">
									{{ $__t('Search for recipes containing this product') }}
								</a>
								@endif
								<div class="dropdown-divider"></div>
								<a class="dropdown-item product-name-cell"
									data-product-id="{{ $stockEntry->product_id }}"
									type="button"
									href="#">
									{{ $__t('Product overview') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal?embedded&product=') }}{{ $stockEntry->product_id }}">
									{{ $__t('Stock journal') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal/summary?embedded&product=') }}{{ $stockEntry->product_id }}">
									{{ $__t('Stock journal summary') }}
								</a>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/product/') }}{{ $stockEntry->product_id . '?returnto=/stockentries' }}">
									{{ $__t('Edit product') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item stockentry-grocycode-link"
									type="button"
									href="{{ $U('/stockentry/' . $stockEntry->id . '/grocycode?download=true') }}">
									{{ $__t('Download stock entry grocycode') }}
								</a>
								@if(GROCY_FEATURE_FLAG_LABELPRINTER)
								<a class="dropdown-item stockentry-grocycode-stockentry-label-print"
									data-stock-id="{{ $stockEntry->id }}"
									type="button"
									href="#">
									{{ $__t('Print stock entry grocycode on label printer') }}
								</a>
								@endif
								<a class="dropdown-item stockentry-label-link"
									type="button"
									target="_blank"
									href="{{ $U('/stockentry/' . $stockEntry->id . '/label') }}">
									{{ $__t('Open stock entry print label in new window') }}
								</a>
							</div>
						</div>
					</td>
					<td class="d-none"
						data-product-id="{{ $stockEntry->product_id }}">
						{{ $stockEntry->product_id }}
					</td>
					<td class="product-name-cell cursor-link"
						data-product-id="{{ $stockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}
					</td>
					<td>
						<span id="stock-{{ $stockEntry->id }}-amount"
							class="locale-number locale-number-quantity-amount">{{ $stockEntry->amount }}</span> <span id="product-{{ $stockEntry->product_id }}-qu-name">{{ $__n($stockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
						<span id="stock-{{ $stockEntry->id }}-opened-amount"
							class="small font-italic">@if($stockEntry->open == 1){{ $__t('Opened') }}@endif</span>
					</td>
					@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
					<td>
						<span id="stock-{{ $stockEntry->id }}-due-date">{{ $stockEntry->best_before_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-due-date-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $stockEntry->best_before_date }} 23:59:59"></time>
					</td>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					<td id="stock-{{ $stockEntry->id }}-location"
						data-location-id="{{ $stockEntry->location_id }}">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $stockEntry->location_id)->name }}
					</td>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<td id="stock-{{ $stockEntry->id }}-shopping-location"
						data-shopping-location-id="{{ $stockEntry->shopping_location_id }}">
						@if (FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id) !== null)
						{{ FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id)->name }}
						@endif
					</td>
					<td id="stock-{{ $stockEntry->id }}-price"
						class="locale-number locale-number-currency"
						data-price-id="{{ $stockEntry->price }}">
						{{ $stockEntry->price }}
					</td>
					@endif
					<td>
						<span id="stock-{{ $stockEntry->id }}-purchased-date">{{ $stockEntry->purchased_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-purchased-date-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $stockEntry->purchased_date }} 23:59:59"></time>
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

<div class="modal fade"
	id="productcard-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.productcard')
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
