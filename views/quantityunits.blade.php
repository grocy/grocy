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
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
			<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=quantity_units') }}">
				<i class="fas fa-sliders-h"></i>&nbsp;{{ $L('Configure userfields') }}
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
	<div class="col">
		<table id="quantityunits-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Description') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($quantityunits as $quantityunit)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/quantityunit/') }}{{ $quantityunit->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm quantityunit-delete-button" href="#" data-quantityunit-id="{{ $quantityunit->id }}" data-quantityunit-name="{{ $quantityunit->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $quantityunit->name }}
					</td>
					<td>
						{{ $quantityunit->description }}
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $quantityunit->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
