@extends('layout.default')

@section('title', $L('Stock overview'))
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title') <small id="info-current-stock" class="text-muted"></small></h1>
		<p id="info-expiring-products" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning no-real-button responsive-button mr-2"></p>
		<p id="info-expired-products" class="btn btn-lg btn-danger no-real-button responsive-button mr-2"></p>
		<p id="info-missing-products" class="btn btn-lg btn-info no-real-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="location-filter">{{ $L('Filter by location') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="location-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($locations as $location)
				<option value="{{ $location->name }}">{{ $location->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Product') }}</th>
					<th>{{ $L('Amount') }}</th>
					<th>{{ $L('Next best before date') }}</th>
					<th class="hidden">Hidden location</th>
				</tr>
			</thead>
			<tbody>
				@foreach($currentStock as $currentStockEntry) 
				<tr id="product-{{ $currentStockEntry->product_id }}-row" class="@if($currentStockEntry->best_before_date < date('Y-m-d', strtotime('-1 days'))) table-danger @elseif($currentStockEntry->best_before_date < date('Y-m-d', strtotime("+$nextXDays days"))) table-warning @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) table-info @endif">
					<td class="fit-content">
						<a class="btn btn-success btn-sm product-consume-button" href="#" title="{{ $L('Consume #3 #1 of #2', FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name, 1) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button" class="btn btn-danger btn-sm product-consume-button" href="#" title="{{ $L('Consume all #1 which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="{{ $currentStockEntry->amount }}">
							<i class="fas fa-utensils"></i> {{ $L('All') }}
						</a>
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-amount">{{ $currentStockEntry->amount }}</span> {{ Pluralize($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}
					</td>
					<td>
						{{ $currentStockEntry->best_before_date }}
						<time class="timeago timeago-contextual" datetime="{{ $currentStockEntry->best_before_date }}"></time>
					</td>
					<td class="hidden">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->location_id)->name }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
