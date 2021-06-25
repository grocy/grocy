@extends($rootLayout)

@section('title', $__t('Batteries journal'))
@section('activeNav', 'batteriesjournal')
@section('viewJsName', 'batteriesjournal')

@php 
$collapsed_none = $embedded ? '' : 'd-md-none';
$collapsed_flex = $embedded ? '' : 'd-md-flex';
@endphp

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<div class="float-right">
			<button class="btn btn-outline-dark {{ $collapsed_none }} mt-2 order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#table-filter-row">
				<i class="fas fa-filter"></i>
			</button>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse {{ $collapsed_flex }}"
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
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Battery') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="battery-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($batteries as $battery)
				<option value="{{ $battery->id }}">{{ $battery->name }}</option>
				@endforeach
			</select>
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

<div class="row mt-2">
	<div class="col">
		<table id="batteries-journal-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#batteries-journal-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Battery') }}</th>
					<th>{{ $__t('Tracked time') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($chargeCycles as $chargeCycleEntry)
				<tr id="charge-cycle-{{ $chargeCycleEntry->id }}-row"
					class="@if($chargeCycleEntry->undone == 1) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-sm undo-battery-execution-button @if($chargeCycleEntry->undone == 1) disabled @endif permission-BATTERIES_UNDO_CHARGE_CYCLE"
							href="#"
							data-charge-cycle-id="{{ $chargeCycleEntry->id }}"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Undo charge cycle') }}">
							<i class="fas fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($chargeCycleEntry->undone == 1) text-strike-through @endif">{{ FindObjectInArrayByPropertyValue($batteries, 'id', $chargeCycleEntry->battery_id)->name }}</span>
						@if($chargeCycleEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $chargeCycleEntry->undone_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $chargeCycleEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						{{ $chargeCycleEntry->tracked_time }}
						<time class="timeago timeago-contextual"
							datetime="{{ $chargeCycleEntry->tracked_time }}"></time>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
