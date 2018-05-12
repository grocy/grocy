@extends('layout.default')

@section('title', $L('Locations'))
@section('activeNav', 'locations')
@section('viewJsName', 'locations')

@section('content')
<h1 class="page-header">
	@yield('title')
	<a class="btn btn-default" href="{{ $U('/location/new') }}" role="button">
		<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
	</a>
</h1>

<div class="table-responsive">
	<table id="locations-table" class="table table-striped">
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
					<a class="btn btn-info" href="{{ $U('/location/') }}{{ $location->id }}" role="button">
						<i class="fa fa-pencil"></i>
					</a>
					<a class="btn btn-danger location-delete-button" href="#" role="button" data-location-id="{{ $location->id }}" data-location-name="{{ $location->name }}">
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
@stop
