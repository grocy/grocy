@extends('layout.default')

@section('title', $__t('Chores'))
@section('activeNav', 'chores')
@section('viewJsName', 'chores')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=chores') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/chore/new') }}">
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
