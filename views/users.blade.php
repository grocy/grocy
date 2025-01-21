@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Users'))

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
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100 m-1 mt-md-0 mb-md-0 float-right"
				id="related-links">
				@if(!defined('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION'))
				<a class="btn btn-primary responsive-button"
					href="{{ $U('/user/new') }}">
					{{ $__t('Add') }}
				</a>
				@endif
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/userfields?entity=users') }}">
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
		<table id="users-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#users-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Username') }}</th>
					<th>{{ $__t('First name') }}</th>
					<th>{{ $__t('Last name') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($users as $user)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm"
							href="{{ $U('/user/') }}{{ $user->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						@if(!GROCY_IS_EMBEDDED_INSTALL && !GROCY_DISABLE_AUTH)
						<a class="btn btn-info btn-sm"
							href="{{ $U('/user/' . $user->id . '/permissions') }}"
							data-toggle="tooltip"
							title="{{ $__t('Configure user permissions') }}">
							<i class="fa-solid fa-lock"></i>
						</a>
						@endif
						<a class="btn btn-danger btn-sm user-delete-button @if($user->id == GROCY_USER_ID) disabled @endif"
							href="#"
							data-user-id="{{ $user->id }}"
							data-user-username="{{ $user->username }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $user->username }}
					</td>
					<td>
						{{ $user->first_name }}
					</td>
					<td>
						{{ $user->last_name }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $user->id)
					))
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
