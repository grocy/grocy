@extends('layout.default')

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
	<div class="col-xs-12 col-md-6 col-xl-3">
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
	<div class="col-xs-12 col-md-6 col-xl-3">
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
							title="{{ $__t('Hide/view columns') }}"
							data-table-selector="#products-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">{{ $__t('Location') }}</th>
					<th>{{ $__t('Min. stock amount') }}</th>
					<th>{{ $__t('Default quantity unit purchase') }}</th>
					<th>{{ $__t('Quantity unit stock') }}</th>
					<th>{{ $__t('Product group') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($products as $product)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm"
							href="{{ $U('/product/') }}{{ $product->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-primary btn-sm"
							href="{{ $U('/product/new?copy-of=') }}{{ $product->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Copy this item') }}">
							<i class="fas fa-copy"></i>
						</a>
						<a class="btn btn-danger btn-sm product-delete-button @if($product->active == 0) disabled @endif"
							href="#"
							data-product-id="{{ $product->id }}"
							data-product-name="{{ $product->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						@if($product->active == 0) (deactivated) @endif {{ $product->name }}@if(!empty($product->picture_file_name)) <i class="fas fa-image text-muted"
							data-toggle="tooltip"
							title="{{ $__t('This product has a picture') }}"></i>@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $product->location_id)->name }}
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
@stop
