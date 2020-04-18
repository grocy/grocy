@extends('layout.default')

@section('title', $__t('Products'))
@section('activeNav', 'products')
@section('viewJsName', 'products')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-secondary mb-1" href="{{ $U('/userfields?entity=products') }}">
					{{ $__t('Configure userfields') }}
				</a>
				<a class="btn btn-outline-secondary mb-1" href="{{ $U('/stocksettings#productpresets') }}">
					{{ $__t('Presets for new products') }}
				</a>
			</div>
		</div>
		<hr>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/product/new') }}">
			{{ $__t('Add') }}
		</a>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
					<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="products-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Name') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">{{ $__t('Location') }}</th>
					<th>{{ $__t('Min. stock amount') }}</th>
					<th>{{ $__t('QU purchase') }}</th>
					<th>{{ $__t('QU stock') }}</th>
					<th>{{ $__t('QU factor') }}</th>
					<th>{{ $__t('Product group') }}</th>
					<th>{{ $__t('Barcode(s)') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($products as $product)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/product/') }}{{ $product->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm product-delete-button" href="#" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $product->name }}@if(!empty($product->picture_file_name)) <i class="fas fa-image text-muted"></i>@endif
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
						<span class="locale-number locale-number-quantity-amount">{{ $product->qu_factor_purchase_to_stock }}</span>
					</td>
					<td>
						@if(!empty($product->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', $product->product_group_id)->name }} @endif
					</td>
					<td>
						{{ $product->barcode }}
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
