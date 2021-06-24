@extends($rootLayout)

@section('title', $userentity->caption)
@section('activeNav', 'userentity-' . $userentity->name)
@section('viewJsName', 'userobjects')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title mr-2 order-0">
				@yield('title')
			</h2>
			<h2 class="mb-0 mr-auto order-3 order-md-1 width-xs-sm-100">
				<span class="text-muted small">{{ $userentity->description }}</span>
			</h2>
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
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100 m-1 mt-md-0 mb-md-0 float-right"
				id="related-links">
				<a class="btn btn-primary responsive-button mr-1 show-as-dialog-link"
					href="{{ $U('/userobject/' . $userentity->name . '/new?embedded') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary d-print-none"
					href="{{ $U('/userfields?entity=' . 'userentity-' . $userentity->name) }}">
					{{ $__t('Configure fields') }}
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

<div class="row">
	<div class="col">
		<table id="userobjects-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right d-print-none"></th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($userobjects as $userobject)
				<tr>
					<td class="fit-content border-right d-print-none">
						<a class="btn btn-info btn-sm show-as-dialog-link"
							href="{{ $U('/userobject/' . $userentity->name . '/') }}{{ $userobject->id }}?embedded"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm userobject-delete-button"
							href="#"
							data-userobject-id="{{ $userobject->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $userobject->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
