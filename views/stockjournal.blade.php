@extends('layout.default')

@section('title', $__t('Stock journal'))
@section('activeNav', 'stockjournal')
@section('viewJsName', 'stockjournal')

@section('content')
<div class="title-related-links">
	<h2 class="title">@yield('title')</h2>
	<div class="float-right">
		<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
			type="button"
			data-toggle="collapse"
			data-target="#table-filter-row">
			<i class="fas fa-filter"></i>
		</button>
		<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3 hide-when-embedded"
			type="button"
			data-toggle="collapse"
			data-target="#related-links">
			<i class="fas fa-ellipsis-v"></i>
		</button>
	</div>
	<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
		id="related-links">
		<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
			href="{{ $U('/stockjournal/summary') }}">
			{{ $__t('Journal summary') }}
		</a>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-2">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Product') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="product-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($products as $product)
				<option value="{{ $product->id }}">{{ $product->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Transaction type') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="transaction-type-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($transactionTypes as $transactionType)
				<option value="{{ $transactionType }}">{{ $__t($transactionType) }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Location') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
				<option value="{{ $location->id }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-2 mt-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('User') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="user-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($users as $user)
				<option value="{{ $user->id }}">{{ $user->display_name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3 mt-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-clock"></i>&nbsp;{{ $__t('Date range') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="daterange-filter">
				<option value="1">{{ $__n(1, '%s month', '%s months') }}</option>
				<option value="6"
					selected>{{ $__n(6, '%s month', '%s months') }}</option>
				<option value="12">{{ $__n(1, '%s year', '%s years') }}</option>
				<option value="24">{{ $__n(2, '%s month', '%s years') }}</option>
				<option value="9999">{{ $__t('All') }}</option>
			</select>
		</div>
	</div>
	<div class="col">
		<div class="float-right mt-1">
			<a id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				href="#">
				{{ $__t('Clear filter') }}
			</a>
		</div>
	</div>
</div>

<div class="row mt-2">
	<div class="col">
		<table id="stock-journal-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#stock-journal-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th>{{ $__t('Transaction time') }}</th>
					<th>{{ $__t('Transaction type') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">{{ $__t('Location') }}</th>
					<th>{{ $__t('Done by') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($stockLog as $stockLogEntry)
				<tr id="stock-booking-{{ $stockLogEntry->id }}-row"
					class="@if($stockLogEntry->undone == 1) text-muted @endif stock-booking-correlation-{{ $stockLogEntry->correlation_id }}"
					data-correlation-id="{{ $stockLogEntry->correlation_id }}">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-xs undo-stock-booking-button @if($stockLogEntry->undone == 1) disabled @endif"
							href="#"
							data-booking-id="{{ $stockLogEntry->id }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Undo transaction') }}">
							<i class="fas fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($stockLogEntry->undone == 1) text-strike-through @endif">{{ $stockLogEntry->product_name }}</span>
						@if($stockLogEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $stockLogEntry->undone_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $stockLogEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $stockLogEntry->amount }}</span> {{ $__n($stockLogEntry->amount, $stockLogEntry->qu_name, $stockLogEntry->qu_name_plural, true) }}
					</td>
					<td>
						{{ $stockLogEntry->row_created_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $stockLogEntry->row_created_timestamp }}"></time>
					</td>
					<td>
						{{ $__t($stockLogEntry->transaction_type) }}
						@if ($stockLogEntry->spoiled == 1)
						<span class="font-italic text-muted">{{ $__t('Spoiled') }}</span>
						@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif">
						{{ $stockLogEntry->location_name }}
					</td>
					<td>
						{{ $stockLogEntry->user_display_name }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
