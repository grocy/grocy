@extends('layout.default')

@section('title', 'Shopping list')
@section('activeNav', 'shoppinglist')
@section('viewJsName', 'shoppinglist')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Shopping list
		<a class="btn btn-default" href="/shoppinglistitem/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
		<a id="add-products-below-min-stock-amount" class="btn btn-info" href="#" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add products that are below defined min. stock amount
		</a>
	</h1>

	<div class="table-responsive">
		<table id="shoppinglist-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Product / <em>Note</em></th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($listItems as $listItem)
				<tr class="@if($listItem->amount_autoadded > 0) info-bg @endif">
					<td class="fit-content">
						<a class="btn btn-info" href="/shoppinglistitem/{{ $listItem->id }}" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger shoppinglist-delete-button" href="#" role="button" data-shoppinglist-id="{{ $listItem->id }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{{ $listItem->note }}</em>
					</td>
					<td>
						{{ $listItem->amount + $listItem->amount_autoadded }} @if(!empty($listItem->product_id))  {{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name }}@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@stop
