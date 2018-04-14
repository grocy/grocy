@extends('layout.default')

@section('title', 'Products')
@section('activeNav', 'products')
@section('viewJsName', 'products')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

	<h1 class="page-header">
		Products
		<a class="btn btn-default" href="/product/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="products-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Location</th>
					<th>Min. stock amount</th>
					<th>QU purchase</th>
					<th>QU stock</th>
					<th>QU factor</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				@foreach($products as $product)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/product/{{ $product->id }}" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger product-delete-button" href="#" role="button" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $product->name }}
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
						{{ $product->description }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@stop
