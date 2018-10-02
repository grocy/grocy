@extends('layout.default')

@section('title', $L('Equipment'))
@section('activeNav', 'equipment')
@section('viewJsName', 'equipment')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/equipment/new') }}">
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

	<div class="col-xs-12 col-md-3 pb-3">
		<table id="equipment-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($equipment as $equipmentItem)
				<tr data-equipment-id="{{ $equipmentItem->id }}">
					<td class="fit-content">
						<a class="btn btn-info btn-sm" href="{{ $U('/equipment/') }}{{ $equipmentItem->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm equipment-delete-button" href="#" data-equipment-id="{{ $equipmentItem->id }}" data-equipment-name="{{ $equipmentItem->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $equipmentItem->name }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-xs-12 col-md-9">
		<h3>{{ $L('Instruction manual') }}</h3>
		<p id="selected-equipment-has-no-instruction-manual-hint">{{ $L('The selected equipment has no instruction manual') }}</p>
		<p>TODO: Here the current instruction manual needs to be shown (PDF.js), if any...</p>
	</div>
</div>
@stop
