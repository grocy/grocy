@extends('layout.default')

@section('title', $L('Batteries'))
@section('activeNav', 'batteries')
@section('viewJsName', 'batteries')

@section('content')
<h1 class="page-header">
	@yield('title')
	<a class="btn btn-default" href="{{ $U('/battery/new') }}" role="button">
		<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
	</a>
</h1>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 no-gutters">
			<label for="search">{{ $L('Search') }}</label>
			<input type="text" class="form-control" id="search">
		</div>
	</div>
</div>

<div class="table-responsive">
	<table id="batteries-table" class="table table-striped">
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
					<a class="btn btn-info" href="{{ $U('/battery/') }}{{ $battery->id }}" role="button">
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
@stop
