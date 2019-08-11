@extends('layout.default')

@section('title', $__t('Location Content Sheet'))
@section('viewJsName', 'locationcontentsheet')

@push('pageStyles')
	<style>
		@media print
		{
			.page:not(:last-child) {
				page-break-after: always !important;
			}

			.page.no-page-break {
				page-break-after: avoid !important;
			}

			/*
				Workaround because of Firefox bug
				see https://github.com/twbs/bootstrap/issues/22753
				and https://bugzilla.mozilla.org/show_bug.cgi?id=1413121
			*/
			.row {
				display: inline !important;
			}
		}
	</style>
@endpush

@section('content')
<h1 class="d-print-none">
	@yield('title')
	<a class="btn btn-outline-dark responsive-button print-all-locations-button" href="#">
		<i class="fas fa-print"></i> {{ $__t('Print') . ' (' . $__t('all locations') . ')' }}
	</a>
</h1>
<h5 class="mb-5 d-print-none">
	<small class="text-muted">{{ $__t('Here you can print a page per location with the current stock, maybe to hang it there and note the consumed things on it.') }}</small>
</h5>

@foreach($locations as $location)
<div class="page">
	<h1 class="text-center">
		<img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}" height="30" class="d-none d-print-flex mx-auto">
		{{ $location->name }}
		<a class="btn btn-outline-dark responsive-button print-single-location-button d-print-none" href="#">
			<i class="fas fa-print"></i> {{ $__t('Print') . ' (' . $__t('this location') . ')' }}
		</a>
	</h1>
	<h6 class="text-center mb-4 d-none d-print-block">
		{{ $__t('Time of printing') }}:
		<span class="d-inline print-timestamp"></span>
	</h6>
	<div class="row w-75">
		<div class="col">
			<table class="table">
				<thead>
					<tr>
						<th>{{ $__t('Product') }}</th>
						<th>{{ $__t('Amount') }}</th>
						<th>{{ $__t('Consumend amount') . ' / ' . $__t('Notes') }}</th>
					</tr>
				</thead>
				<tbody>
					@php $currentStockEntriesForLocation = FindAllObjectsInArrayByPropertyValue($currentStockLocationContent, 'location_id', $location->id); @endphp
					@foreach($currentStockEntriesForLocation as $currentStockEntry) 
					<tr>
						<td>
							{{ FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name }}
						</td>
						<td>
							<span>{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
							<span class="small font-italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						</td>
						<td class=""></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endforeach
@stop
