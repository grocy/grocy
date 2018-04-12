@extends('layout.default')

@section('title', 'Quantity units')
@section('activeNav', 'quantityunits')
@section('viewJsName', 'quantityunits')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Quantity units
		<a class="btn btn-default" href="/quantityunit/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="quantityunits-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				@foreach($quantityunits as $quantityunit)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/quantityunit/{{ $quantityunit->id }}" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger quantityunit-delete-button" href="#" role="button" data-quantityunit-id="{{ $quantityunit->id }}" data-quantityunit-name="{{ $quantityunit->name }}">
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
