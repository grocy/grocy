@extends($rootLayout)

@section('title', $__t('Equipment'))
@section('activeNav', 'equipment')
@section('viewJsName', 'equipment')

@section('content')
<div class="row">
	<div class="col-12 col-md-4 pb-3">
		<div class="title-related-links border-bottom mb-2 py-1">
			<h2 class="title">@yield('title')</h2>
			<div class="float-right">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fas fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fas fa-ellipsis-v"></i>
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
						<span class="input-group-text"><i class="fas fa-search"></i></span>
					</div>
					<input type="text"
						id="search"
						class="form-control"
						placeholder="{{ $__t('Search') }}">
				</div>
			</div>
			<div class="col">
				<div class="float-right">
					<a id="clear-filter-button"
						class="btn btn-sm btn-outline-info"
						href="#">
						{{ $__t('Clear filter') }}
					</a>
				</div>
			</div>
		</div>

		<table id="equipment-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#equipment-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
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
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger hide-when-embedded hide-on-fullscreen-card equipment-delete-button"
							href="#"
							data-equipment-id="{{ $equipmentItem->id }}"
							data-equipment-name="{{ $equipmentItem->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
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

	<div class="col-12 col-md-8">
		<ul class="nav nav-tabs grocy-tabs">
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
		</ul>
		<div class="tab-content grocy-tabs">
			<div class="tab-pane fade show active"
				id="instruction-manual-tab">
				<div id="selectedEquipmentInstructionManualCard"
					class="card">
					<div class="card-header card-header-fullscreen">
						<span class="selected-equipment-name"></span>
						<a id="selectedEquipmentInstructionManualToggleFullscreenButton"
							class="btn btn-sm btn-outline-secondary py-0 float-right mr-1"
							href="#"
							data-toggle="tooltip"
							title="{{ $__t('Expand to fullscreen') }}">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
						<a id="selectedEquipmentInstructionManualDownloadButton"
							class="btn btn-sm btn-outline-secondary py-0 float-right mr-1"
							href="#"
							target="_blank"
							data-toggle="tooltip"
							title="{{ $__t('Download file') }}">
							<i class="fas fa-file-download"></i>
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
							<i class="fas fa-expand-arrows-alt"></i>
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
