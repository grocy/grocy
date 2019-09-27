@extends('layout.default')

@section('title', $userentity->caption)
@section('activeNav', 'userentity-' . $userentity->name)
@section('viewJsName', 'userobjects')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark d-print-none" href="{{ $U('/userobject/' . $userentity->name . '/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $__t('Add') }}
			</a>
			<a class="btn btn-outline-secondary d-print-none" href="{{ $U('/userfields?entity=' . 'userentity-' . $userentity->name) }}">
				<i class="fas fa-sliders-h"></i>&nbsp;{{ $__t('Configure fields') }}
			</a>
		</h1>
		<h5 class="text-muted">{{ $userentity->description }}</h5>
	</div>
</div>

<div class="row mt-3 d-print-none">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
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
