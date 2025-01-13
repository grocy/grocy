@php require_frontend_packages(['datatables', 'daterangepicker', 'chartjs']); @endphp

@extends('layout.default')

@section('title', $__t('Stock report') . ' / ' . $__t('Spendings'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title mr-2 order-0">
				@yield('title')
			</h2>
			<div class="float-right @if($embedded) pr-5 @endif">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fa-solid fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fa-solid fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-link responsive-button m-1 mt-md-0 mb-md-0 discrete-link disabled"
					href="#">
					{{ $__t('Group by') }}:
				</a>
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right group-by-button @if($groupBy == 'product') active @endif"
					href="#"
					data-group-by="product">
					{{ $__t('Product') }}
				</a>
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right group-by-button @if($groupBy == 'productgroup') active @endif"
					href="#"
					data-group-by="productgroup">
					{{ $__t('Product group') }}
				</a>
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right group-by-button @if($groupBy == 'store') active @endif"
					href="#"
					data-group-by="store">
					{{ $__t('Store') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-sm-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-clock"></i>&nbsp;{{ $__t('Date range') }}</span>
			</div>
			<input type="text"
				name="date-filter"
				id="daterange-filter"
				class="custom-control custom-select"
				value="" />
		</div>
	</div>
	@if($groupBy == 'product')
	<div class="col-sm-12 col-md-6 col-xl-4">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option @if($productGroup->id == $selectedGroup) selected="selected" @endif
					value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
				@endforeach
				<option class="font-italic font-weight-light"
					value="ungrouped">{{ $__t('Ungrouped') }}</option>
			</select>
		</div>
	</div>
	@endif
	<div class="col">
		<div class="float-right">
			<button id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				data-toggle="tooltip"
				title="{{ $__t('Clear filter') }}">
				<i class="fa-solid fa-filter-circle-xmark"></i>
			</button>
		</div>
	</div>
</div>

<div class="row mt-2">
	<div class="col-sm-12 col-md-12 col-xl-12">
		<canvas id="metrics-chart"></canvas>
	</div>
	<div class="col-sm-12 col-md-12 col-xl-12">
		<table id="metrics-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Total') }}</th>
					@if($groupBy == 'product')
					<th>{{ $__t('Product group') }}</th>
					@endif
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($metrics as $metric)
				<tr>
					<td>
						@if($groupBy == 'productgroup')
						@if(empty($metric->name))
						<span class="font-italic font-weight-light">{{ $__t('Ungrouped') }}</span>
						@else
						{{ $metric->name }}
						@endif
						@else
						{{ $metric->name }}
						@endif
					</td>
					<td data-chart-value="{{ $metric->total }}"
						data-order="{{ $metric->total }}">
						<span class="locale-number locale-number-currency">{{ $metric->total }}</span>
					</td>
					@if($groupBy == 'product')
					<td>
						@if(empty($metric->group_name))
						<span class="font-italic font-weight-light">{{ $__t('Ungrouped') }}</span>
						@else
						{{ $metric->group_name }}
						@endif
					</td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
