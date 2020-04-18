@extends('layout.default')

@section('title', $__t('Batteries'))
@section('activeNav', 'batteries')
@section('viewJsName', 'batteries')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-secondary" href="{{ $U('/userfields?entity=batteries') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/battery/new') }}">
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
		<table id="batteries-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Description') }}</th>
					<th>{{ $__t('Used in') }}</th>
					<th>{{ $__t('Charge cycle interval (days)') }}</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($batteries as $battery)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/battery/') }}{{ $battery->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm battery-delete-button" href="#" data-battery-id="{{ $battery->id }}" data-battery-name="{{ $battery->name }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $battery->name }}
					</td>
					<td>
						{{ $battery->description }}
					</td>
					<td>
						{{ $battery->used_in }}
					</td>
					<td>
						{{ $battery->charge_interval_days }}
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $battery->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
