@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Equipment'))

@section('content')
<div class="row">
	<div class="col-12 col-md-4 pb-3">
		<div class="title-related-links border-bottom mb-2 py-1">
			<h2 class="title">@yield('title')</h2>
			<div class="float-right @if($embedded) pr-5 @endif">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fa-solid fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fa-solid fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/equipment/new') }}">
					{{ $__t('Add') }}
				</a>
			</div>
		</div>

		<div class="row collapse d-md-flex"
			id="table-filter-row">
			<div class="col">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="fa-solid fa-search"></i></span>
					</div>
					<input type="text"
						id="search"
						class="form-control"
						placeholder="{{ $__t('Search') }}">
				</div>
			</div>
			<div class="col">
				<div class="float-right">
					<button id="clear-filter-button"
						class="btn btn-sm btn-outline-info"
						data-toggle="tooltip"
						title="{{ $__t('Clear filter') }}">
						<i class="fa-solid fa-filter-circle-xmark"></i>
					</button>
				</div>
			</div>
		</div>

		<table id="equipment-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#equipment-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields,
					'excludeFieldTypes' => [\Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE]
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($equipment as $equipmentItem)
				<tr data-equipment-id="{{ $equipmentItem->id }}">
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm hide-when-embedded hide-on-fullscreen-card"
							href="{{ $U('/equipment/') }}{{ $equipmentItem->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right hide-on-fullscreen-card hide-when-embedded">
								<a class="dropdown-item equipment-delete-button"
									type="button"
									href="#"
									data-equipment-id="{{ $equipmentItem->id }}"
									data-equipment-name="{{ $equipmentItem->name }}">
									<span class="dropdown-item-text">{{ $__t('Delete this item') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td>
						{{ $equipmentItem->name }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $equipmentItem->id),
					'excludeFieldTypes' => [\Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE]
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-12 col-md-8">
		<ul class="nav nav-tabs grocy-tabs mb-1">
			<li class="nav-item">
				<a class="nav-link active"
					data-toggle="tab"
					href="#instruction-manual-tab">{{ $__t('Instruction manual') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link"
					data-toggle="tab"
					href="#description-tab">{{ $__t('Notes') }}</a>
			</li>
			@foreach($userfields as $userfield)
			@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE)
			<li class="nav-item">
				<a class="nav-link"
					data-toggle="tab"
					href="#file-userfield-{{$userfield->name}}-tab">{{ $userfield->caption }}</a>
			</li>
			@endif
			@endforeach
		</ul>
		<div class="tab-content grocy-tabs">
			<div class="tab-pane fade show active"
				id="instruction-manual-tab">
				<div class="card selectedEquipmentInstructionManualCard">
					<div class="card-header card-header-fullscreen">
						<span class="selected-equipment-name"></span>
						<a class="btn btn-sm btn-outline-secondary py-0 float-right mr-1 selectedEquipmentInstructionManualToggleFullscreenButton"
							href="#"
							data-toggle="tooltip"
							title="{{ $__t('Expand to fullscreen') }}">
							<i class="fa-solid fa-expand-arrows-alt"></i>
						</a>
						<a id="selectedEquipmentInstructionManualDownloadButton"
							class="btn btn-sm btn-outline-secondary py-0 float-right mr-1"
							href="#"
							target="_blank"
							data-toggle="tooltip"
							title="{{ $__t('Download file') }}">
							<i class="fa-solid fa-file-download"></i>
						</a>
					</div>
					<div class="card-body py-0 px-0">
						<p id="selected-equipment-has-no-instruction-manual-hint"
							class="text-muted font-italic d-none pt-3 pl-3">{{ $__t('The selected equipment has no instruction manual') }}</p>
						<embed id="selected-equipment-instruction-manual"
							class="embed-responsive embed-responsive-4by3"
							src=""
							type="application/pdf">
					</div>
				</div>
			</div>
			@foreach($userfields as $userfield)
			@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE)
			<div class="tab-pane fade"
				id="file-userfield-{{$userfield->name}}-tab">
				<div class="card selectedEquipmentInstructionManualCard">
					<div class="card-header card-header-fullscreen">
						<span class="selected-equipment-name"></span>
						<a class="btn btn-sm btn-outline-secondary py-0 float-right mr-1 selectedEquipmentInstructionManualToggleFullscreenButton"
							href="#"
							data-toggle="tooltip"
							title="{{ $__t('Expand to fullscreen') }}">
							<i class="fa-solid fa-expand-arrows-alt"></i>
						</a>
						<a id="file-userfield-{{$userfield->name}}-download-button"
							class="btn btn-sm btn-outline-secondary py-0 float-right mr-1"
							href="#"
							target="_blank"
							data-toggle="tooltip"
							title="{{ $__t('Download file') }}">
							<i class="fa-solid fa-file-download"></i>
						</a>
					</div>
					<div class="card-body py-0 px-0">
						<p id="file-userfield-{{$userfield->name}}-empty-hint"
							class="text-muted font-italic d-none pt-3 pl-3"></p>
						<embed id="file-userfield-{{$userfield->name}}-embed"
							class="embed-responsive embed-responsive-4by3"
							src=""
							type="application/pdf">
					</div>
				</div>
			</div>
			@endif
			@endforeach
			<div class="tab-pane fade"
				id="description-tab">
				<div id="selectedEquipmentDescriptionCard"
					class="card">
					<div class="card-header card-header-fullscreen">
						<span class="selected-equipment-name"></span>
						<a id="selectedEquipmentDescriptionToggleFullscreenButton"
							class="btn btn-sm btn-outline-secondary py-0 float-right"
							href="#"
							data-toggle="tooltip"
							title="{{ $__t('Expand to fullscreen') }}">
							<i class="fa-solid fa-expand-arrows-alt"></i>
						</a>
					</div>
					<div class="card-body">
						<div id="description-tab-content"
							class="mb-0"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
