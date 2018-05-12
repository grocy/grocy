@extends('layout.default')

@section('title', $L('Quantity units'))
@section('activeNav', 'quantityunits')
@section('viewJsName', 'quantityunits')

@section('content')
<h1 class="page-header">
	@yield('title')
	<a class="btn btn-default" href="{{ $U('/quantityunit/new') }}" role="button">
		<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
	</a>
</h1>

<div class="table-responsive">
	<table id="quantityunits-table" class="table table-striped">
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
					<a class="btn btn-info" href="{{ $U('/quantityunit/') }}{{ $quantityunit->id }}" role="button">
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
@stop
