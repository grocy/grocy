@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Batteries'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
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
				<a class="btn btn-primary responsive-button permission-MASTER_DATA_EDIT m-1 mt-md-0 mb-md-0 float-right show-as-dialog-link"
					href="{{ $U('/battery/new?embedded') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/userfields?entity=batteries') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
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
	<div class="col-12 col-md-6 col-xl-3">
		<div class="form-check custom-control custom-checkbox">
			<input class="form-check-input custom-control-input"
				type="checkbox"
				id="show-disabled">
			<label class="form-check-label custom-control-label"
				for="show-disabled">
				{{ $__t('Show disabled') }}
			</label>
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

<div class="row">
	<div class="col">
		<table id="batteries-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#batteries-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Description') }}</th>
					<th class="allow-grouping">{{ $__t('Used in') }}</th>
					<th class="allow-grouping">{{ $__t('Charge cycle interval (days)') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($batteries as $battery)
				<tr class="@if($battery->active == 0) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm permission-MASTER_DATA_EDIT show-as-dialog-link"
							href="{{ $U('/battery/') }}{{ $battery->id }}?embedded"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm battery-delete-button permission-MASTER_DATA_EDIT"
							href="#"
							data-battery-id="{{ $battery->id }}"
							data-battery-name="{{ $battery->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
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
					<td>
						{{ $battery->charge_interval_days }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $battery->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
