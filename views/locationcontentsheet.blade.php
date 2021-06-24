@extends($rootLayout)

@section('title', $__t('Location Content Sheet'))
@section('viewJsName', 'locationcontentsheet')

@push('pageStyles')
<style>
	@media print {
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
<div class="title-related-links d-print-none">
	<h2 class="title">
		@yield('title')
		<i class="fas fa-question-circle text-muted small"
			data-toggle="tooltip"
			title="{{ $__t('Here you can print a page per location with the current stock, maybe to hang it there and note the consumed things on it') }}"></i>
	</h2>
	<div class="float-right">
		<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
			type="button"
			data-toggle="collapse"
			data-target="#related-links">
			<i class="fas fa-ellipsis-v"></i>
		</button>
	</div>
	<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
		id="related-links">
		<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right print-all-locations-button"
			href="#">
			{{ $__t('Print') . ' (' . $__t('all locations') . ')' }}
		</a>
	</div>
</div>

<hr class="my-2 d-print-none">

@foreach($locations as $location)
<div class="page">
	<h1 class="pt-4 text-center">
		<img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}"
			height="30"
			class="d-none d-print-flex mx-auto">
		{{ $location->name }}
		<a class="btn btn-outline-dark btn-sm responsive-button print-single-location-button d-print-none"
			href="#">
			{{ $__t('Print') . ' (' . $__t('this location') . ')' }}
		</a>
	</h1>
	<h6 class="mb-4 d-none d-print-block text-center">
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
						<th>{{ $__t('Consumed amount') . ' / ' . $__t('Notes') }}</th>
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
							<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->qu_id_stock)->name_plural) }}</span>
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
