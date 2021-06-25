@extends($rootLayout)

@section('title', $__t('Stock journal summary'))
@section('activeNav', '')
@section('viewJsName', 'stockjournalsummary')

@php 
$collapsed_none = $embedded ? '' : 'd-md-none';
$collapsed_flex = $embedded ? '' : 'd-md-flex';
@endphp

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right">
			<button class="btn btn-outline-dark {{ $collapsed_none }} mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fas fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse {{ $collapsed_flex }}"
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
		<table id="stock-journal-summary-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#stock-journal-summary-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Transaction type') }}</th>
					<th>{{ $__t('User') }}</th>
					<th>{{ $__t('Amount') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($entries as $journalEntry)
				<tr>
					<td class="fit-content border-right"></td>
					<td>
						{{ $journalEntry->product_name }}
					</td>
					<td>
						{{ $__t($journalEntry->transaction_type) }}
					</td>
					<td>
						{{ $journalEntry->user_display_name }}
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $journalEntry->amount }}</span> {{ $__n($journalEntry->amount, $journalEntry->qu_name, $journalEntry->qu_name_plural) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
