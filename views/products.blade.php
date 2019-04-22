@extends('layout.default')

@section('title', $L('Products'))
@section('activeNav', 'products')
@section('viewJsName', 'products')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/product/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
			<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=products') }}">
				<i class="fas fa-sliders-h"></i>&nbsp;{{ $L('Configure userfields') }}
			</a>
			<a class="btn btn-outline-secondary" href="{{ $U('/stocksettings#productpresets') }}">
				<i class="fas fa-sliders-h"></i>&nbsp;{{ $L('Presets for new products') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter">{{ $L('Filter by product group') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="product-group-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
			@endforeach
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="products-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Location') }}</th>
					<th>{{ $L('Min. stock amount') }}</th>
					<th>{{ $L('QU purchase') }}</th>
					<th>{{ $L('QU stock') }}</th>
					<th>{{ $L('QU factor') }}</th>
					<th>{{ $L('Product group') }}</th>

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
					<td>
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $product->location_id)->name }}
					</td>
					<td>
						{{ $product->min_stock_amount }}
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_purchase)->name }}
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $product->qu_id_stock)->name }}
					</td>
					<td>
						{{ $product->qu_factor_purchase_to_stock }}
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
