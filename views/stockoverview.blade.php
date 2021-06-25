@extends($rootLayout)

@section('title', $__t('Stock overview'))
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title mr-2 order-0">
				@yield('title')
			</h2>
			<h2 class="mb-0 mr-auto order-3 order-md-1 width-xs-sm-100">
				<span id="info-current-stock"
					class="text-muted small"></span>
			</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fas fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockjournal') }}">
					{{ $__t('Journal') }}
				</a>
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockentries') }}">
					{{ $__t('Stock entries') }}
				</a>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/locationcontentsheet') }}">
					{{ $__t('Location Content Sheet') }}
				</a>
				@endif
			</div>
		</div>
		<div class="border-top border-bottom my-2 py-1">
			@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<div id="info-duesoon-products"
				data-next-x-days="{{ $nextXDays }}"
				data-status-filter="duesoon"
				class="warning-message status-filter-message responsive-button mr-2"></div>
			<div id="info-overdue-products"
				data-status-filter="overdue"
				class="secondary-message status-filter-message responsive-button mr-2"></div>
			<div id="info-expired-products"
				data-status-filter="expired"
				class="error-message status-filter-message responsive-button mr-2"></div>
			@endif
			<div id="info-missing-products"
				data-status-filter="belowminstockamount"
				class="normal-message status-filter-message responsive-button"></div>
			<div class="float-right">
				<a class="btn btn-sm btn-outline-info d-md-none mt-1"
					data-toggle="collapse"
					href="#table-filter-row"
					role="button">
					<i class="fas fa-filter"></i>
				</a>
				<a id="clear-filter-button"
					class="btn btn-sm btn-outline-info mt-1"
					href="#">
					{{ $__t('Clear filter') }}
				</a>
			</div>
		</div>
	</div>
</div>
<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Location') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
				<option value="{{ $location->name }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="status-filter">
				<option class="bg-white"
					value="all">{{ $__t('All') }}</option>
				@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				<option value="duesoon">{{ $__t('Due soon') }}</option>
				<option value="overdue">{{ $__t('Overdue') }}</option>
				<option value="expired">{{ $__t('Expired') }}</option>
				@endif
				<option value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<div class="dropdown">
			<div class="table-inline-menu dropdown-menu detached-dropdown-menu dropdown-menu-right" id="datatable-dropdown">
				@include('components.stockentrydropdowncommon')
				<div class="dropdown-divider"></div>
				<a class="dropdown-item stockentry-grocycode-link"
					type="button"
					data-href="{{ $U('/product/PRODUCT_ID/grocycode?download=true') }}">
					{{ $__t('Download product grocycode') }}
				</a>
				@if(GROCY_FEATURE_FLAG_LABELPRINTER)
				<a class="dropdown-item stockentry-grocycode-product-label-print"
					data-product-id="xxx"
					type="button"
					href="#">
					{{ $__t('Print product grocycode on label printer') }}
				</a>
				@endif
			</div>
		</div>
	

		<table id="stock-overview-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#stock-overview-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Product group') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Value') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">{{ $__t('Next due date') }}</th>
					<th class="d-none">Hidden location</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden product group</th>
					<th>{{ $__t('Calories') }} ({{ $__t('Per stock quantity unit') }})</th>
					<th>{{ $__t('Calories') }}</th>
					<th>{{ $__t('Last purchased') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Last price') }}</th>
					<th>{{ $__t('Min. stock amount') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentStock as $currentStockEntry)
				<tr id="product-{{ $currentStockEntry->product_id }}-row"
					class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) @if($currentStockEntry->due_type == 1) table-secondary @else table-danger @endif @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('+' . $nextXDays . ' days')) && $currentStockEntry->amount > 0) table-warning @elseif ($currentStockEntry->product_missing) table-info @endif">
					<td class="fit-content border-right">
						<a class="permission-STOCK_CONSUME btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_consume_amount || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Consume %1$s of %2$s', floatval($currentStockEntry->quick_consume_amount) . ' ' . $currentStockEntry->qu_unit_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-consume-amount="{{ $currentStockEntry->quick_consume_amount }}">
							<i class="fas fa-utensils"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_consume_amount }}</span>
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button"
							class="permission-STOCK_CONSUME btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount_aggregated == 0) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Consume all %s which are currently in stock', $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-consume-amount="@if($currentStockEntry->enable_tare_weight_handling == 1){{$currentStockEntry->tare_weight}}@else{{$currentStockEntry->amount}}@endif"
							data-original-total-stock-amount="{{$currentStockEntry->amount}}">
							<i class="fas fa-utensils"></i> {{ $__t('All') }}
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_consume_amount || $currentStockEntry->amount_aggregated == $currentStockEntry->amount_opened_aggregated || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark %1$s of %2$s as open', floatval($currentStockEntry->quick_consume_amount) . ' ' . $currentStockEntry->qu_unit_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-open-amount="{{ $currentStockEntry->quick_consume_amount }}">
							<i class="fas fa-box-open"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_consume_amount }}</span>
						</a>
						@endif
						<button class="btn btn-sm btn-light text-secondary"
							type="button"
							id="detached-dropdown-{!! uniqid() !!}"
							data-toggle="dropdown-detached"
							data-target="#datatable-dropdown"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_unit_name }}"
							data-transfer="{{ ($currentStockEntry->amount < 1 ? 1 : 0) }}"
							data-consume="{{ ($currentStockEntry->amount < 1 ? 1 : 0) }}"
							data-location-id="">
							<i class="fas fa-ellipsis-v"></i>
						</button>
					</td>
					<td class="product-name-cell cursor-link"
						data-product-id="{{ $currentStockEntry->product_id }}">
						{{ $currentStockEntry->product_name }}
					</td>
					<td>
						@if($currentStockEntry->product_group_name !== null){{ $currentStockEntry->product_group_name }}@endif
					</td>
					<td data-order={{
						$currentStockEntry->amount }}>
						<span id="product-{{ $currentStockEntry->product_id }}-amount"
							class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, $currentStockEntry->qu_unit_name, $currentStockEntry->qu_unit_name_plural) }}</span>
						<span id="product-{{ $currentStockEntry->product_id }}-opened-amount"
							class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						@if($currentStockEntry->is_aggregated_amount == 1)
						<span class="pl-1 text-secondary">
							<i class="fas fa-custom-sigma-sign"></i> <span id="product-{{ $currentStockEntry->product_id }}-amount-aggregated"
								class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount_aggregated }}</span> {{ $__n($currentStockEntry->amount_aggregated, $currentStockEntry->qu_unit_name, $currentStockEntry->qu_unit_name_plural) }}
							@if($currentStockEntry->amount_opened_aggregated > 0)<span id="product-{{ $currentStockEntry->product_id }}-opened-amount-aggregated"
								class="small font-italic">{{ $__t('%s opened', $currentStockEntry->amount_opened_aggregated) }}</span>@endif
						</span>
						@endif
						@if(boolval($userSettings['show_icon_on_stock_overview_page_when_product_is_on_shopping_list']))
						@if($currentStockEntry->on_shopping_list)
						<span class="text-muted cursor-normal"
							data-toggle="tooltip"
							title="{{ $__t('This product is currently on a shopping list') }}">
							<i class="fas fa-shopping-cart"></i>
						</span>
						@endif
						@endif
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-value"
							class="locale-number locale-number-currency">{{ $currentStockEntry->value }}</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) d-none @endif">
						<span id="product-{{ $currentStockEntry->product_id }}-next-due-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="product-{{ $currentStockEntry->product_id }}-next-due-date-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $currentStockEntry->best_before_date }} 23:59:59"></time>
					</td>
					<td class="d-none">
						@foreach(FindAllObjectsInArrayByPropertyValue($currentStockLocations, 'product_id', $currentStockEntry->product_id) as $locationsForProduct)
						xx{{ FindObjectInArrayByPropertyValue($locations, 'id', $locationsForProduct->location_id)->name }}xx
						@endforeach
					</td>
					<td class="d-none">
						@if($currentStockEntry->best_before_date < date('Y-m-d
							23:59:59',
							strtotime('-'
							. '1'
							. ' days'
							))
							&&
							$currentStockEntry->amount > 0) @if($currentStockEntry->due_type == 1) overdue @else expired @endif @elseif($currentStockEntry->best_before_date < date('Y-m-d
								23:59:59',
								strtotime('+'
								.
								$nextXDays
								. ' days'
								))
								&&
								$currentStockEntry->amount > 0) duesoon @elseif ($currentStockEntry->product_missing) belowminstockamount @endif"
					</td>
					<td class="d-none">
						xx{{ $currentStockEntry->product_group_name }}xx
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->product_calories }}</span>
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->calories }}</span>
					</td>
					<td>
						{{ $currentStockEntry->last_purchased }}
						<time class="timeago timeago-contextual"
							datetime="{{ $currentStockEntry->last_purchased }}"></time>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">
						<span class="locale-number locale-number-currency">{{ $currentStockEntry->last_price }}</span>
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->min_stock_amount }}</span>
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

<div class="modal fade"
	id="stockoverview-productcard-modal"
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
