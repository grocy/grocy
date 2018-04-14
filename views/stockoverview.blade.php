@extends('layout.default')

@section('title', 'Stock overview')
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

	<h1 class="page-header">Stock overview <span class="text-muded small"><strong>{{ count($currentStock) }}</strong> products with <strong>{{ SumArrayValue($currentStock, 'amount') }}</strong> units in stock</span></h1>

	<div class="container-fluid">
		<div class="row">
			<p class="btn btn-lg btn-warning no-real-button"><strong>{{ count(FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime('+5 days')), '<')) }}</strong> products expiring within the next 5 days</p>
			<p class="btn btn-lg btn-danger no-real-button"><strong>{{ count(FindAllObjectsInArrayByPropertyValue($currentStock, 'best_before_date', date('Y-m-d', strtotime('-1 days')), '<')) }}</strong> products are already expired</p>
			<p class="btn btn-lg btn-info no-real-button"><strong>{{ count($missingProducts) }}</strong> products are below defined min. stock amount</p>
		</div>
	</div>

	<div class="discrete-content-separator-2x"></div>

	<div class="table-responsive">
		<table id="stock-overview-table" class="table table-striped">
			<thead>
				<tr>
					<th>Product</th>
					<th>Amount</th>
					<th>Next best before date</th>
				</tr>
			</thead>
			<tbody>
				@foreach($currentStock as $currentStockEntry) 
				<tr class="@if($currentStockEntry->best_before_date < date('Y-m-d', strtotime('-1 days'))) error-bg @elseif($currentStockEntry->best_before_date < date('Y-m-d', strtotime('+5 days'))) warning-bg @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) info-bg @endif">
					<td>
						{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
					</td>
					<td>
						{{ $currentStockEntry->amount . ' ' . FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}
					</td>
					<td>
						{{ $currentStockEntry->best_before_date }}
						<time class="timeago timeago-contextual" datetime="{{ $currentStockEntry->best_before_date }}"></time>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@stop
