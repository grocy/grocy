@extends('layout.default')

@section('title', 'Batteries')
@section('activeNav', 'batteries')
@section('viewJsName', 'batteries')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Batteries
		<a class="btn btn-default" href="/battery/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="batteries-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Description</th>
					<th>Used in</th>
				</tr>
			</thead>
			<tbody>
				@foreach($batteries as $battery)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/battery/{{ $battery->id }}" role="button">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger battery-delete-button" href="#" role="button" data-battery-id="{{ $battery->id }}" data-battery-name="{{ $battery->name }}">
							<i class="fa fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $battery->name }}
					</td>
					<td>
						{{ $battery->description }}
					</td>
					<td>
						{{ $battery->used_in }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@stop
