@extends('layout.default')

@section('title', $__t('Chores'))
@section('activeNav', 'chores')
@section('viewJsName', 'chores')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/chore/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $__t('Add') }}
			</a>
			<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=chores') }}">
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
		<table id="chores-table" class="table table-sm table-striped dt-responsive">
		<thead>
			<tr>
				<th class="border-right"></th>
				<th>{{ $__t('Name') }}</th>
				<th>{{ $__t('Period type') }}</th>
				<th>{{ $__t('Description') }}</th>

				@include('components.userfields_thead', array(
					'userfields' => $userfields
				))

			</tr>
		</thead>
		<tbody class="d-none">
			@foreach($chores as $chore)
			<tr>
				<td class="fit-content border-right">
					<a class="btn btn-info btn-sm" href="{{ $U('/chore/') }}{{ $chore->id }}">
						<i class="fas fa-edit"></i>
					</a>
					<a class="btn btn-danger btn-sm chore-delete-button" href="#" data-chore-id="{{ $chore->id }}" data-chore-name="{{ $chore->name }}">
						<i class="fas fa-trash"></i>
					</a>
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
@stop
