@extends('layout.default')

@section('title', $__t('Stores'))
@section('activeNav', 'shoppinglocations')
@section('viewJsName', 'shoppinglocations')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-primary responsive-button"
					href="{{ $U('/shoppinglocation/new') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn btn-outline-secondary"
					href="{{ $U('/userfields?entity=shoppinglocations') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2 py-1">

<div class="row">
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
</div>

<div class="row">
	<div class="col">
		<table id="shoppinglocations-table"
			class="table table-sm table-striped dt-responsive">
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
