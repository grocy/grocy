@extends('layout.default')

@section('title', $__t('Shopping list'))
@section('activeNav', 'shoppinglist')
@section('viewJsName', 'shoppinglist')

@push('pageScripts')
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script>
		$('.item-row').click(function (id){
			$(this).toggleClass('text-strike-through bg-secondary text-light');
		});

		// Set this page to a fullscreen card to prevent auto-reloading on database change events
		$('body').addClass('fullscreen-card');

		// Ask for confirmation if user wants to leave. Data is not saved.
		$(window).bind('beforeunload', function(){ return ''});
	</script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
	<h2>Offline {{$__t('Shopping list')}}</h2>
<table id="shoppinglist-table" class="table table-sm table-striped">
	<thead>
	<tr>
		<th class="border-right d-none"></th>
		<th>{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
		<th>{{ $__t('Amount') }}</th>
		<th class="d-none">Hiden product group</th>
		<th class="d-none">Hidden status</th>
	</tr>
	</thead>
	<tbody class="d-none">
	@foreach($listItems as $listItem)
		<tr id="shoppinglistitem-{{ $listItem->id }}-row" class="item-row">
			<td class="d-none"></td>
			<td>
				@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@else<em>{!! nl2br($listItem->note) !!}</em>@endif
			</td>
			<td>
				{{ $listItem->amount }} @if(!empty($listItem->product_id)){{ $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural) }}@endif
			</td>
			<td class="d-none">
				@if(!empty(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)->name }} @else <span class="font-italic font-weight-light">{{ $__t('Ungrouped') }}</span> @endif
			</td>
			<td class="d-none">
				@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) belowminstockamount @endif
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@stop
