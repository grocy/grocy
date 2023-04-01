@extends('layout.default')

@section('title', $__t('Stock Metrics: Purchases'))
@section('viewJsName', 'metrics')

@push('pageScripts')
<script src="{{ $U('/node_modules/chart.js/dist/Chart.min.js?v=', true) }}{{ $version }}"></script>
<script src="{{ $U('/node_modules/chartjs-plugin-doughnutlabel/dist/chartjs-plugin-doughnutlabel.js?v=', true) }}{{ $version }}"></script>
<script src="{{ $U('/node_modules/chartjs-plugin-piechart-outlabels/dist/chartjs-plugin-piechart-outlabels.js?v=', true) }}{{ $version}}"></script>
<script src="{{ $U('/node_modules/daterangepicker/daterangepicker.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
<link href="{{ $U('/node_modules/daterangepicker/daterangepicker.css?v=', true) }}{{ $version }}"
	rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title mr-2 order-0">
				@yield('title')
			</h2>
			<h2 class="mb-0 mr-auto order-3 order-md-1 width-xs-sm-100">
				<span id="info-current-stock"
					class="text-muted small"></span>
			</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-outline-dark responsive-button @if(!$byGroup) active @endif m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockmetricspurchases') }}">
					{{ $__t('by Product') }}
				</a>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-outline-dark responsive-button @if($byGroup) active @endif m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/stockmetricspurchases?byGroup=true') }}">
					{{ $__t('by Group') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-sm-12 col-md-6 col-xl-4">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-clock"></i>&nbsp;{{ $__t('Date range') }}</span>
				<input type="text"
					name="date-filter"
					id="daterange-filter"
					class="custom-control custom-select"
					value="" />
			</div>
		</div>
	</div>
	@if(!$byGroup)
	<div class="col-sm-12 col-md-6 col-xl-4">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option @if($selectedGroup==$productGroup->id)
					selected="selected"
					@endif
					value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="col">
		<div class="float-right mt-1">
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
	<div id="chart-wrapper"
		class="col-sm-12 col-md-12 col-xl-12">
		<canvas id="metrics-chart"></canvas>
	</div>
	<div class="col-sm-12 col-md-12 col-xl-12">
		<table id="metrics-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Total') }}</th>
					@if(!$byGroup)
					<th>{{ $__t('Product group') }}</th>
					@endif
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($metrics as $metric)
				<tr>
					<td data-chart-label="{{ $metric->name }}">
						{{ $metric->name }}
					</td>
					<td data-chart-value="{{ $metric->total }}"
						data-order="{{ $metric->total }}">
						<span class="locale-number locale-number-currency">{{ $metric->total }}</span>
					</td>
					@if(!$byGroup)
					<td>
						{{ $metric->group_name }}
					</td>
					@endif
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
