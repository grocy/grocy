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
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#instruction-manual-tab">{{ $L('Instruction manual') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#description-tab">{{ $L('Notes') }}</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="instruction-manual-tab">
				<div id="selectedEquipmentInstructionManualCard" class="card">
					<div class="card-header">
						<i class="fas fa-toolbox"></i> <span class="selected-equipment-name"></span>
						<a id="selectedEquipmentInstructionManualToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $L('Expand to fullscreen') }}">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</div>
					<div class="card-body">
						<p id="selected-equipment-has-no-instruction-manual-hint" class="text-muted font-italic d-none">{{ $L('The selected equipment has no instruction manual') }}</p>
						<embed id="selected-equipment-instruction-manual" class="embed-responsive embed-responsive-4by3" width="100%" height="800px" src="" type="application/pdf">
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="description-tab">
				<div id="selectedEquipmentDescriptionCard" class="card">
					<div class="card-header">
						<i class="fas fa-toolbox"></i> <span class="selected-equipment-name"></span>
						<a id="selectedEquipmentDescriptionToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $L('Expand to fullscreen') }}">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</div>
					<div class="card-body">
						<div id="description-tab-content" class="mb-0"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
