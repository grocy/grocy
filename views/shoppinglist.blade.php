@extends('layout.default')

@section('title', $L('Shopping list'))
@section('activeNav', 'shoppinglist')
@section('viewJsName', 'shoppinglist')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/shoppinglistitem/new') }}">
				<i class="fas fa-plus"></i> {{ $L('Add') }}
			</a>
			<a id="clear-shopping-list" class="btn btn-outline-danger responsive-button @if($listItems->count() == 0) disabled @endif" href="#">
				<i class="fas fa-trash"></i> {{ $L('Clear list') }}
			</a>
			<a id="add-products-below-min-stock-amount" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-cart-plus"></i> {{ $L('Add products that are below defined min. stock amount') }}
			</a>
			<a id="add-all-items-to-stock-button" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-box"></i> {{ $L('Add all list items to stock') }}
			</a>
		</h1>
		<p data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button">{{ Pluralize(count($missingProducts), $L('#1 product is below defined min. stock amount', count($missingProducts)), $L('#1 products are below defined min. stock amount', count($missingProducts))) }}</p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-4">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-4">
		<label for="status-filter">{{ $L('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $L('All') }}</option>
			<option class="bg-info" value="belowminstockamount">{{ $L('Below min. stock amount') }}</option>
		</select>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-8 pb-3">
		<table id="shoppinglist-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th></th>
					<th>{{ $L('Product') }} / <em>{{ $L('Note') }}</em></th>
					<th>{{ $L('Amount') }}</th>
					<th class="d-none">Hiden product group</th>
					<th class="d-none">Hidden status</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($listItems as $listItem)
				<tr id="shoppinglistitem-{{ $listItem->id }}-row" class="@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) table-info @endif">
					<td class="fit-content">
						<a class="btn btn-sm btn-info" href="{{ $U('/shoppinglistitem/') }}{{ $listItem->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger shoppinglist-delete-button" href="#" data-shoppinglist-id="{{ $listItem->id }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary @if(empty($listItem->product_id)) disabled @else shopping-list-stock-add-workflow-list-item-button @endif" href="{{ $U('/purchase?embedded&flow=shoppinglistitemtostock&product=') }}{{ $listItem->product_id }}&amount={{ $listItem->amount }}&listitemid={{ $listItem->id }}" @if(!empty($listItem->product_id)) data-toggle="tooltip" title="{{ $L('Add #3 #1 of #2 to stock', Pluralize($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural), FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name, $listItem->amount) }}" @endif>
							<i class="fas fa-box"></i>
						</a>
					</td>
					<td>
						@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{!! nl2br($listItem->note) !!}</em>
					</td>
					<td>
						{{ $listItem->amount }} @if(!empty($listItem->product_id)){{ Pluralize($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural) }}@endif
					</td>
					<td class="d-none">
						@if(!empty(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)->name }} @else <span class="font-italic font-weight-light">{{ $L('Ungrouped') }}</span> @endif
					</td>
					<td class="d-none">
						@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) belowminstockamount @endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-xs-12 col-md-4 mt-md-2">
		@include('components.calendarcard')
	</div>
</div>

<div class="modal fade" id="shopping-list-stock-add-workflow-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				<iframe id="shopping-list-stock-add-workflow-purchase-form-frame" class="embed-responsive" src=""></iframe>
			</div>
			<div class="modal-footer">
				<span id="shopping-list-stock-add-workflow-purchase-item-count" class="d-none mr-auto"></span>
				<button id="shopping-list-stock-add-workflow-skip-button" type="button" class="btn btn-primary"><i class="fas fa-angle-double-right"></i> {{ $L('Skip') }}</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $L('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
