@extends('layout.default')

@section('title', $__t('Equipment'))
@section('activeNav', 'equipment')
@section('viewJsName', 'equipment')

@section('content')
<div class="row">

	<div class="col-xs-12 col-md-4 pb-3">
		<h2 class="title">@yield('title')</h2>
		<hr>

		<div class="row">
			<div class="col-xs-12 col-md-4 col-xl-3">
				<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/equipment/new') }}">
					{{ $__t('Add') }}
				</a>
			</div>
		</div>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text" id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>

		<table id="equipment-table" class="table table-striped dt-responsive">
			<thead>
				<tr>
					<th>{{ $__t('Name') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
					
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($equipment as $equipmentItem)
				<tr data-equipment-id="{{ $equipmentItem->id }}">
					<td>
						{{ $equipmentItem->name }}
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $equipmentItem->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-xs-12 col-md-8">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#instruction-manual-tab">{{ $__t('Instruction manual') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#description-tab">{{ $__t('Notes') }}</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="instruction-manual-tab">
				<div id="selectedEquipmentInstructionManualCard" class="card">
					<div class="card-header card-header-fullscreen">
						<i class="fas fa-toolbox"></i> <span class="selected-equipment-name"></span>&nbsp;&nbsp;
						<a class="btn btn-sm btn-outline-info py-0 equipment-edit-button hide-on-fullscreen-card" href="#">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-outline-danger py-0 equipment-delete-button hide-on-fullscreen-card" href="#" data-equipment-id="{{ $equipmentItem->id }}" data-equipment-name="{{ $equipmentItem->name }}">
							<i class="fas fa-trash"></i>
						</a>
						<a id="selectedEquipmentInstructionManualToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $__t('Expand to fullscreen') }}">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</div>
					<div class="card-body py-0 px-0">
						<p id="selected-equipment-has-no-instruction-manual-hint" class="text-muted font-italic d-none pt-3 pl-3">{{ $__t('The selected equipment has no instruction manual') }}</p>
						<embed id="selected-equipment-instruction-manual" class="embed-responsive embed-responsive-4by3" src="" type="application/pdf">
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="description-tab">
				<div id="selectedEquipmentDescriptionCard" class="card">
					<div class="card-header card-header-fullscreen">
						<i class="fas fa-toolbox"></i> <span class="selected-equipment-name"></span>&nbsp;&nbsp;
						<a class="btn btn-sm btn-outline-info py-0 equipment-edit-button hide-on-fullscreen-card" href="#">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-outline-danger py-0 equipment-delete-button hide-on-fullscreen-card" href="#" data-equipment-id="{{ $equipmentItem->id }}" data-equipment-name="{{ $equipmentItem->name }}">
							<i class="fas fa-trash"></i>
						</a>
						<a id="selectedEquipmentDescriptionToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $__t('Expand to fullscreen') }}">
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
