@extends($rootLayout)

@section('title', $__t('Products'))
@section('activeNav', 'products')
@section('viewJsName', 'products')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="float-right">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fas fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fas fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/product/new') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/userfields?entity=products') }}">
					{{ $__t('Configure userfields') }}
				</a>
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stocksettings#productpresets') }}">
					{{ $__t('Presets for new products') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

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
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="form-check custom-control custom-checkbox">
			<input class="form-check-input custom-control-input"
				type="checkbox"
				id="show-disabled">
			<label class="form-check-label custom-control-label"
				for="show-disabled">
				{{ $__t('Show disabled') }}
			</label>
		</div>
	</div>
	<div class="col">
		<div class="float-right">
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
		<table id="products-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#products-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">{{ $__t('Location') }}</th>
					<th>{{ $__t('Min. stock amount') }}</th>
					<th>{{ $__t('Default quantity unit purchase') }}</th>
					<th>{{ $__t('Quantity unit stock') }}</th>
					<th>{{ $__t('Product group') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">{{ $__t('Default store') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($products as $product)
				<tr class="@if($product->active == 0) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm"
							href="{{ $U('/product/') }}{{ $product->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm product-delete-button"
							href="#"
							data-product-id="{{ $product->id }}"
							data-product-name="{{ $product->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/product/new?copy-of=') }}{{ $product->id }}">
									<span class="dropdown-item-text">{{ $__t('Copy') }}</span>
								</a>
								<a class="dropdown-item merge-products-button"
									data-product-id="{{ $product->id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Merge') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td>
						{{ $product->name }}
						@if(!empty($product->picture_file_name))
						<i class="fas fa-image text-muted"
							data-toggle="tooltip"
							title="{{ $__t('This product has a picture') }}"></i>
						@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">
						@php
						$location = FindObjectInArrayByPropertyValue($locations, 'id', $product->location_id);
						@endphp
						@if($location != null)
						{{ $location->name }}
						@endif
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $product->min_stock_amount }}</span>
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_purchase)->name }}
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_stock)->name }}
					</td>
					<td>
						@if(!empty($product->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', $product->product_group_id)->name }} @endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif">
						@php
						$store = FindObjectInArrayByPropertyValue($shoppingLocations, 'id', $product->shopping_location_id);
						@endphp
						@if($store != null)
						{{ $store->name }}
						@endif
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $product->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade"
	id="merge-products-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 class="modal-title w-100">{{ $__t('Merge products') }}</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="merge-products-keep">{{ $__t('Product to keep') }}&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('After merging, this product will be kept') }}"></i>
					</label>
					<select class="custom-control custom-select"
						id="merge-products-keep">
						<option></option>
						@foreach($products as $product)
						<option value="{{ $product->id }}">{{ $product->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="merge-products-remove">{{ $__t('Product to remove') }}&nbsp;<i class="fas fa-question-circle text-muted"
							data-toggle="tooltip"
							title="{{ $__t('After merging, all occurences of this product will be replaced by "Product to keep" (means this product will not exist anymore)') }}"></i>
					</label>
					<select class="custom-control custom-select"
						id="merge-products-remove">
						<option></option>
						@foreach($products as $product)
						<option value="{{ $product->id }}">{{ $product->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="merge-products-save-button"
					type="button"
					class="btn btn-primary"
					data-dismiss="modal">{{ $__t('OK') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
