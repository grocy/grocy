@extends('layout.default')

@section('title', $userentity->caption)
@section('activeNav', 'userentity-' . $userentity->name)
@section('viewJsName', 'userobjects')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-secondary d-print-none" href="{{ $U('/userfields?entity=' . 'userentity-' . $userentity->name) }}">
					{{ $__t('Configure fields') }}
				</a>
			</div>
		</div>
		<h5 class="text-muted">{{ $userentity->description }}</h5>
	</div>
</div>
<hr>
<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/userobject/' . $userentity->name . '/new') }}">
			{{ $__t('Add') }}
		</a>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="userobjects-table" class="table table-sm table-striped dt-responsive">
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
						<a class="btn btn-info btn-sm" href="{{ $U('/userobject/' . $userentity->name . '/') }}{{ $userobject->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm userobject-delete-button" href="#" data-userobject-id="{{ $userobject->id }}">
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
