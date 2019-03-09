@extends('layout.default')

@section('title', $L('Product groups'))
@section('activeNav', 'productgroups')
@section('viewJsName', 'productgroups')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/productgroup/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add') }}
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
		<table id="productgroups-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Description') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($productGroups as $productGroup)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/productgroup/') }}{{ $productGroup->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm product-group-delete-button" href="#" data-group-id="{{ $productGroup->id }}" data-group-name="{{ $productGroup->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $productGroup->name }}
					</td>
					<td>
						{{ $productGroup->description }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
