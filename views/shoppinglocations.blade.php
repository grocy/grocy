@extends('layout.default')

@section('title', $__t('Stores'))
@section('activeNav', 'shoppinglocations')
@section('viewJsName', 'shoppinglocations')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
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
					href="{{ $U('/shoppinglocation/new') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/userfields?entity=shoppinglocations') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2 py-1">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-xs-12 col-md-6 col-xl-3">
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
		<table id="shoppinglocations-table"
			class="table table-sm table-striped nowrap w-100">
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
				@foreach($shoppinglocations as $shoppinglocation)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm"
							href="{{ $U('/shoppinglocation/') }}{{ $shoppinglocation->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm shoppinglocation-delete-button"
							href="#"
							data-shoppinglocation-id="{{ $shoppinglocation->id }}"
							data-shoppinglocation-name="{{ $shoppinglocation->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $shoppinglocation->name }}
					</td>
					<td>
						{{ $shoppinglocation->description }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $shoppinglocation->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
