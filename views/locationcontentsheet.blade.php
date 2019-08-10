@extends('layout.default')

@section('title', $__t('Location Content Sheet'))

@push('pageStyles')
	<style>
		@media print
		{
			.page {
				page-break-after: always !important;
			}
		}
	</style>
@endpush

@section('content')
<h1 class="d-print-none">
	<a class="btn btn-outline-dark responsive-button" href="javascript:window.print();">
		<i class="fas fa-print"></i> {{ $__t('Print') }}
	</a>
</h1>

@foreach($locations as $location)
<div class="page">
	<h1>{{ $location->name }}</h1>
	@php $currentStockEntriesForLocation = FindAllObjectsInArrayByPropertyValue($currentStockLocationContent, 'location_id', $location->id); @endphp
	<div class="row w-50">
		<div class="col">
			<table class="table table-sm table-striped">
				<thead>
					<tr>
						<th>{{ $__t('Product') }}</th>
						<th>{{ $__t('Amount') }}</th>
						<th></th>			
					</tr>
				</thead>
				<tbody>
					@foreach($currentStockEntriesForLocation as $currentStockEntry) 
					<tr>
						<td>
							{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
						</td>
						<td>
							<span>{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
							<span class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						</td>
						<td class="fit-content">____________________</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endforeach
@stop
