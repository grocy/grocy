@extends('layout.default')

@section('title', $L('Stock overview'))
@section('activeNav', 'stockoverview')
@section('viewJsName', 'stockoverview')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<style>
		.product-name-cell[data-product-has-picture='true'] {
			cursor: pointer;
		}
	</style>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')
			<small id="info-current-stock" class="text-muted"></small>
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/stockjournal') }}">
				<i class="fas fa-file-alt"></i> {{ $L('Journal') }}
			</a>
		</h1>
		<p id="info-expiring-products" data-next-x-days="{{ $nextXDays }}" data-status-filter="expiring" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-expired-products" data-status-filter="expired" class="btn btn-lg btn-danger status-filter-button responsive-button mr-2"></p>
		<p id="info-missing-products" data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button"></p>
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
		<label for="location-filter">{{ $L('Filter by product group') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="product-group-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $L('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $L('All') }}</option>
			<option class="bg-warning" value="expiring">{{ $L('Expiring soon') }}</option>
			<option class="bg-danger" value="expired">{{ $L('Already expired') }}</option>
			<option class="bg-info" value="belowminstockamount">{{ $L('Below min. stock amount') }}</option>
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
					<th class="d-none">Hidden location</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden product group</th>
				</tr>
			</thead>
			<tbody>
				@foreach($currentStock as $currentStockEntry) 
				<tr id="product-{{ $currentStockEntry->product_id }}-row" class="@if($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) table-danger @elseif($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0) table-warning @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) table-info @endif">
					<td class="fit-content">
						<a class="btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $L('Consume #3 #1 of #2', FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name, 1) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="1">
							<i class="fas fa-utensils"></i> 1
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button" class="btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="right" title="{{ $L('Consume all #1 which are currently in stock', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}"
							data-consume-amount="{{ $currentStockEntry->amount }}">
							<i class="fas fa-utensils"></i> {{ $L('All') }}
						</a>
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount == 0) disabled @endif" href="#" data-toggle="tooltip" data-placement="left" title="{{ $L('Mark #3 #1 of #2 as open', FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name, 1) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}"
							data-product-qu-name="{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name }}">
							<i class="fas fa-box-open"></i> 1
						</a>
						<a class="btn btn-info btn-sm" href="{{ $U('/stockjournal?product=') }}{{ $currentStockEntry->product_id }}">
							<i class="fas fa-file-alt"></i>
						</a>
					</td>
					<td class="product-name-cell" data-product-id="{{ $currentStockEntry->product_id }}">
						{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }} <i class="fas fa-info text-muted"></i>
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-amount">{{ $currentStockEntry->amount }}</span> {{ Pluralize($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}
						<span id="product-{{ $currentStockEntry->product_id }}-opened-amount" class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $L('#1 opened', $currentStockEntry->amount_opened) }}@endif</span>
					</td>
					<td>
						<span id="product-{{ $currentStockEntry->product_id }}-next-best-before-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="product-{{ $currentStockEntry->product_id }}-next-best-before-date-timeago" class="timeago timeago-contextual" datetime="{{ $currentStockEntry->best_before_date }} 23:59:59"></time>
					</td>
					<td class="d-none">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->location_id)->name }}
					</td>
					<td class="d-none">
						@if($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) expired @elseif($currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime("+$nextXDays days")) && $currentStockEntry->amount > 0) expiring @elseif (FindObjectInArrayByPropertyValue($missingProducts, 'id', $currentStockEntry->product_id) !== null) belowminstockamount @endif
					</td>
					@php $productGroup = FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->product_group_id) @endphp
					<td class="d-none">
						@if($productGroup !== null){{ $productGroup->name }}@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="stockoverview-productcard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.productcard')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $L('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
