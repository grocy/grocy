@extends('layout.default')

@section('title', $__t('Task categories'))
@section('activeNav', 'taskcategories')
@section('viewJsName', 'taskcategories')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/taskcategory/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $__t('Add') }}
			</a>
			<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=task_categories') }}">
				<i class="fas fa-sliders-h"></i>&nbsp;{{ $__t('Configure userfields') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="taskcategories-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Description') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($taskCategories as $taskCategory)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/taskcategory/') }}{{ $taskCategory->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm task-category-delete-button" href="#" data-category-id="{{ $taskCategory->id }}" data-category-name="{{ $taskCategory->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $taskCategory->name }}
					</td>
					<td>
						{{ $taskCategory->description }}
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $taskCategory->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
