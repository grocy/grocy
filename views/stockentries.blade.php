@php require_frontend_packages(['datatables', 'animatecss']); @endphp

@extends('layout.default')

@section('title', $__t('Stock entries'))

@push('pageScripts')
<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right @if($embedded) pr-5 @endif">
			<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fa-solid fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3 hide-when-embedded">
		@include('components.productpicker', array(
		'products' => $products,
		'disallowAllProductWorkflows' => true,
		'isRequired' => false,
		'additionalGroupCssClasses' => 'mb-0'
		))
	</div>
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="col-12 col-md-6 col-xl-3 mt-auto">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Location') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
				<option value="{{ $location->id }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="col mt-auto">
		<div class="float-right mt-3">
			<button id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				data-toggle="tooltip"
				title="{{ $__t('Clear filter') }}">
				<i class="fa-solid fa-filter-circle-xmark"></i>
			</button>
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
							title="{{ $__t('Table options') }}"
							data-table-selector="#stockentries-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th class="d-none">Hidden product_id</th> <!-- This must be in the first column for searching -->
					<th class="allow-grouping">{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif allow-grouping">{{ $__t('Due date') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif allow-grouping">{{ $__t('Location') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif allow-grouping">{{ $__t('Store') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Price') }}</th>
					<th class="allow-grouping"
						data-shadow-rowgroup-column="9">{{ $__t('Purchased date') }}</th>
					<th class="d-none">Hidden purchased_date</th>
					<th>{{ $__t('Timestamp') }}</th>
					<th>{{ $__t('Note') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfieldsProducts
					))

					@include('components.userfields_thead', array(
					'userfields' => $userfieldsStock
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
							<i class="fa-solid fa-utensils"></i>
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($stockEntry->open == 1 || FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->enable_tare_weight_handling == 1 || FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->disable_open == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark this stock entry as open') }}"
							data-product-id="{{ $stockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
							data-stock-id="{{ $stockEntry->stock_id }}"
							data-stockrow-id="{{ $stockEntry->id }}"
							data-open-amount="{{ $stockEntry->amount }}">
							<i class="fa-solid fa-box-open"></i>
						</a>
						@endif
						<a class="btn btn-info btn-sm show-as-dialog-link"
							href="{{ $U('/stockentry/' . $stockEntry->id . '?embedded') }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Edit stock entry') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&list=1&product=' . $stockEntry->product_id ) }}">
									<i class="fa-solid fa-shopping-cart"></i> {{ $__t('Add to shopping list') }}
								</a>
								<div class="dropdown-divider"></div>
								@endif
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/purchase?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fa-solid fa-cart-plus"></i> {{ $__t('Purchase') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/consume?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fa-solid fa-utensils"></i> {{ $__t('Consume') }}
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/transfer?embedded&product=' . $stockEntry->product_id . '&locationId=' . $stockEntry->location_id . '&stockId=' . $stockEntry->stock_id) }}">
									<i class="fa-solid fa-exchange-alt"></i> {{ $__t('Transfer') }}
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/inventory?embedded&product=' . $stockEntry->product_id ) }}">
									<i class="fa-solid fa-list"></i> {{ $__t('Inventory') }}
								</a>
								<a class="dropdown-item stock-consume-button stock-consume-button-spoiled"
									type="button"
									href="#"
									data-product-id="{{ $stockEntry->product_id }}"
									data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}"
									data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name }}"
									data-stock-id="{{ $stockEntry->stock_id }}"
									data-stockrow-id="{{ $stockEntry->id }}"
									data-location-id="{{ $stockEntry->location_id }}"
									data-consume-amount="{{ $stockEntry->amount }}">
									{{ $__t('Consume this stock entry as spoiled', '1 ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name) }}
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<div class="dropdown-divider"></div>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/recipes?search=') }}{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}">
									{{ $__t('Search for recipes containing this product') }}
								</a>
								@endif
								<div class="dropdown-divider"></div>
								<a class="dropdown-item productcard-trigger"
									data-product-id="{{ $stockEntry->product_id }}"
									type="button"
									href="#">
									{{ $__t('Product overview') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal?embedded&product=') }}{{ $stockEntry->product_id }}"
									data-dialog-type="table">
									{{ $__t('Stock journal') }}
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal/summary?embedded&product=') }}{{ $stockEntry->product_id }}"
									data-dialog-type="table">
									{{ $__t('Stock journal summary') }}
								</a>
								<a class="dropdown-item link-return"
									type="button"
									data-href="{{ $U('/product/') }}{{ $stockEntry->product_id }}">
									{{ $__t('Edit product') }}
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/stockentry/' . $stockEntry->id . '/grocycode?download=true') }}">
									{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Download %s Grocycode', $__t('Stock entry'))) !!}
								</a>
								@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
								<a class="dropdown-item stockentry-grocycode-label-print"
									data-stock-id="{{ $stockEntry->id }}"
									type="button"
									href="#">
									{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Print %s Grocycode on label printer', $__t('Stock entry'))) !!}
								</a>
								@endif
								<a class="dropdown-item stockentry-label-link"
									type="button"
									target="_blank"
									href="{{ $U('/stockentry/' . $stockEntry->id . '/label') }}">
									{{ $__t('Open stock entry label in new window') }}
								</a>
							</div>
						</div>
					</td>
					<td class="d-none"
						data-product-id="{{ $stockEntry->product_id }}">
						{{ $stockEntry->product_id }}
					</td>
					<td class="productcard-trigger cursor-link"
						data-product-id="{{ $stockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->name }}
					</td>
					<td>
						<span class="custom-sort d-none">{{$stockEntry->amount}}</span>
						<span id="stock-{{ $stockEntry->id }}-amount"
							class="locale-number locale-number-quantity-amount">{{ $stockEntry->amount }}</span> <span id="product-{{ $stockEntry->product_id }}-qu-name">{{ $__n($stockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name_plural, true) }}</span>
						<span id="stock-{{ $stockEntry->id }}-opened-amount"
							class="small font-italic">@if($stockEntry->open == 1){{ $__n($stockEntry->amount, 'Opened', 'Opened') }}@endif</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">
						<span id="stock-{{ $stockEntry->id }}-due-date">{{ $stockEntry->best_before_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-due-date-timeago"
							class="timeago timeago-contextual"
							@if($stockEntry->best_before_date != "") datetime="{{ $stockEntry->best_before_date }} 23:59:59" @endif></time>
					</td>
					<td id="stock-{{ $stockEntry->id }}-location"
						class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif"
						data-location-id="{{ $stockEntry->location_id }}">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $stockEntry->location_id)->name }}
					</td>
					<td id="stock-{{ $stockEntry->id }}-shopping-location"
						class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif"
						data-shopping-location-id="{{ $stockEntry->shopping_location_id }}">
						@if (FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id) !== null)
						{{ FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $stockEntry->shopping_location_id)->name }}
						@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">
						<span class="custom-sort d-none">{{$stockEntry->price}}</span>
						<span id="stock-{{ $stockEntry->id }}-price"
							data-toggle="tooltip"
							data-trigger="hover click"
							data-html="true"
							title="{!! $__t('%1$s per %2$s', '<span class=\'locale-number locale-number-currency\'>' . $stockEntry->price . '</span>', FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_stock)->name) !!}">
							{!! $__t('%1$s per %2$s', '<span class="locale-number locale-number-currency">' . $stockEntry->price * $stockEntry->qu_factor_price_to_stock . '</span>', FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockEntry->product_id)->qu_id_price)->name) !!}
						</span>
					</td>
					<td>
						<span id="stock-{{ $stockEntry->id }}-purchased-date">{{ $stockEntry->purchased_date }}</span>
						<time id="stock-{{ $stockEntry->id }}-purchased-date-timeago"
							class="timeago timeago-contextual"
							@if(!empty($stockEntry->purchased_date)) datetime="{{ $stockEntry->purchased_date }} 23:59:59" @endif></time>
					</td>
					<td class="d-none">{{ $stockEntry->purchased_date }}</td>
					<td>
						<span>{{ $stockEntry->row_created_timestamp }}</span>
						<time class="timeago timeago-contextual"
							datetime="{{ $stockEntry->row_created_timestamp }}"></time>
					</td>
					<td>
						<span id="stock-{{ $stockEntry->id }}-note">{{ $stockEntry->note }}</span>
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfieldsProducts,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValuesProducts, 'object_id', $stockEntry->product_id)
					))

					@include('components.userfields_tbody', array(
					'userfields' => $userfieldsStock,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValuesStock, 'object_id', $stockEntry->stock_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@include('components.productcard', [
'asModal' => true
])
@stop
