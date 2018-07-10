@extends('layout.default')

@section('title', $L('Locations'))
@section('activeNav', 'locations')
@section('viewJsName', 'locations')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/location/new') }}">
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
		<table id="locations-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Description') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($locations as $location)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info btn-sm" href="{{ $U('/location/') }}{{ $location->id }}">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger btn-sm location-delete-button" href="#" data-location-id="{{ $location->id }}" data-location-name="{{ $location->name }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $location->name }}
					</td>
					<td>
						{{ $location->description }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
