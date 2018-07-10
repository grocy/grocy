@extends('layout.default')

@section('title', $L('Batteries'))
@section('activeNav', 'batteries')
@section('viewJsName', 'batteries')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/battery/new') }}">
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
		<table id="batteries-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Description') }}</th>
					<th>{{ $L('Used in') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($batteries as $battery)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info btn-sm" href="{{ $U('/battery/') }}{{ $battery->id }}">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger btn-sm battery-delete-button" href="#" data-battery-id="{{ $battery->id }}" data-battery-name="{{ $battery->name }}">
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
