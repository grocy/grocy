@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Chores'))

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
				<a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/chore/new') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/userfields?entity=chores') }}">
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
		<table id="chores-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#chores-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Name') }}</th>
					<th class="allow-grouping">{{ $__t('Period type') }}</th>
					<th>{{ $__t('Description') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($chores as $chore)
				<tr class="@if($chore->active == 0) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm"
							href="{{ $U('/chore/') }}{{ $chore->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm chore-delete-button"
							href="#"
							data-chore-id="{{ $chore->id }}"
							data-chore-name="{{ $chore->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item merge-chores-button"
									data-chore-id="{{ $chore->id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Merge') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td>
						{{ $chore->name }}
					</td>
					<td>
						{{ $__t($chore->period_type) }}
					</td>
					<td>
						{{ $chore->description }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $chore->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade"
	id="merge-chores-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 class="modal-title w-100">{{ $__t('Merge chores') }}</h4>
			</div>
			<div class="modal-body">
				<form id="merge-chores-form"
					novalidate>

					<div class="form-group">
						<label for="merge-chores-keep">{{ $__t('Chore to keep') }}&nbsp;<i class="fa-solid fa-question-circle text-muted"
								data-toggle="tooltip"
								data-trigger="hover click"
								title="{{ $__t('After merging, this chore will be kept') }}"></i>
						</label>
						<select class="custom-control custom-select"
							id="merge-chores-keep"
							required>
							<option></option>
							@foreach($chores as $chore)
							<option value="{{ $chore->id }}">{{ $chore->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="merge-chores-remove">{{ $__t('Chore to remove') }}&nbsp;<i class="fa-solid fa-question-circle text-muted"
								data-toggle="tooltip"
								data-trigger="hover click"
								title="{{ $__t('After merging, all occurences of this chore will be replaced by the kept chore (means this chore will not exist anymore)') }}"></i>
						</label>
						<select class="custom-control custom-select"
							id="merge-chores-remove"
							required>
							<option></option>
							@foreach($chores as $chore)
							<option value="{{ $chore->id }}">{{ $chore->name }}</option>
							@endforeach
						</select>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="merge-chores-save-button"
					type="button"
					class="btn btn-primary">{{ $__t('OK') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
