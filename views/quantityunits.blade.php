@extends('layout.default')

@section('title', $L('Quantity units'))
@section('activeNav', 'quantityunits')
@section('viewJsName', 'quantityunits')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/quantityunit/new') }}">
				<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-3">
		<label for="search">{{ $L('Search') }}</label>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="quantityunits-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Description') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($quantityunits as $quantityunit)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info btn-sm" href="{{ $U('/quantityunit/') }}{{ $quantityunit->id }}">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger btn-sm quantityunit-delete-button" href="#" data-quantityunit-id="{{ $quantityunit->id }}" data-quantityunit-name="{{ $quantityunit->name }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $quantityunit->name }}
					</td>
					<td>
						{{ $quantityunit->description }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
