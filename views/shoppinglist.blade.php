@extends('layout.default')

@section('title', $L('Shopping list'))
@section('activeNav', 'shoppinglist')
@section('viewJsName', 'shoppinglist')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/shoppinglistitem/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
			<a id="add-products-below-min-stock-amount" class="btn btn-info" href="#">
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add products that are below defined min. stock amount') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-8 pb-3">
		<table id="shoppinglist-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Product') }} / <em>{{ $L('Note') }}</em></th>
					<th>{{ $L('Amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($listItems as $listItem)
				<tr class="@if($listItem->amount_autoadded > 0) table-info @endif">
					<td class="fit-content">
						<a class="btn btn-sm btn-info" href="{{ $U('/shoppinglistitem/') }}{{ $listItem->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger shoppinglist-delete-button" href="#" data-shoppinglist-id="{{ $listItem->id }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{{ $listItem->note }}</em>
					</td>
					<td>
						{{ $listItem->amount + $listItem->amount_autoadded }} @if(!empty($listItem->product_id))  {{ FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name }}@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-xs-12 col-md-4">
		@include('components.calendarcard')
	</div>
</div>
@stop
