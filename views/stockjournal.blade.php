@extends('layout.default')

@section('title', $__t('Stock journal'))
@section('activeNav', 'stockjournal')
@section('viewJsName', 'stockjournal')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr>
<div class="row my-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="product-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($products as $product)
					<option value="{{ $product->id }}">{{ $product->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="stock-journal-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th>{{ $__t('Booking time') }}</th>
					<th>{{ $__t('Booking type') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">{{ $__t('Location') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($stockLog as $stockLogEntry)
				<tr id="stock-booking-{{ $stockLogEntry->id }}-row" class="@if($stockLogEntry->undone == 1) text-muted @endif stock-booking-correlation-{{ $stockLogEntry->correlation_id }}" data-correlation-id="{{ $stockLogEntry->correlation_id }}">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-sm undo-stock-booking-button @if($stockLogEntry->undone == 1) disabled @endif" href="#" data-booking-id="{{ $stockLogEntry->id }}" data-toggle="tooltip" data-placement="left" title="{{ $__t('Undo booking') }}">
							<i class="fas fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($stockLogEntry->undone == 1) text-strike-through @endif">{{ FindObjectInArrayByPropertyValue($products, 'id', $stockLogEntry->product_id)->name }}</span>
						@if($stockLogEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $stockLogEntry->undone_timestamp }}
						<time class="timeago timeago-contextual" datetime="{{ $stockLogEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $stockLogEntry->amount }}</span> {{ $__n($stockLogEntry->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockLogEntry->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $stockLogEntry->product_id)->qu_id_stock)->name_plural) }}
					</td>
					<td>
						{{ $stockLogEntry->row_created_timestamp }}
						<time class="timeago timeago-contextual" datetime="{{ $stockLogEntry->row_created_timestamp }}"></time>
					</td>
					<td>
						{{ $__t($stockLogEntry->transaction_type) }}
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">
						{{ FindObjectInArrayByPropertyValue($locations, 'id', $stockLogEntry->location_id)->name }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
