@extends('layout.default')

@section('title', $L('Userfields'))
@section('activeNav', 'userfields')
@section('viewJsName', 'userfields')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a id="new-userfield-button" class="btn btn-outline-dark" href="{{ $U('/userfield/new') }}">
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
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="entity-filter">{{ $L('Filter by entity') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="entity-filter">
			<option value="all">{{ $L('All') }}</option>
			@foreach($entities as $entity)
				<option value="{{ $entity }}">{{ $entity }}</option>
			@endforeach
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="userfields-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $L('Entity') }}</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Caption') }}</th>
					<th>{{ $L('Type') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($userfields as $userfield)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/userfield/') }}{{ $userfield->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm userfield-delete-button" href="#" data-userfield-id="{{ $userfield->id }}" data-userfield-name="{{ $userfield->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $userfield->entity }}
					</td>
					<td>
						{{ $userfield->name }}
					</td>
					<td>
						{{ $userfield->caption }}
					</td>
					<td>
						{{ $L($userfield->type) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
