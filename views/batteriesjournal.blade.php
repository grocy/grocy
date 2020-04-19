@extends('layout.default')

@section('title', $__t('Batteries journal'))
@section('activeNav', 'batteriesjournal')
@section('viewJsName', 'batteriesjournal')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr>
<div class="row my-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="battery-filter">
				<option value="all">{{ $__t('All') }}</option>
			@foreach($batteries as $battery)
				<option value="{{ $battery->id }}">{{ $battery->name }}</option>
			@endforeach
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="batteries-journal-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Battery') }}</th>
					<th>{{ $__t('Tracked time') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($chargeCycles as $chargeCycleEntry)
				<tr id="charge-cycle-{{ $chargeCycleEntry->id }}-row" class="@if($chargeCycleEntry->undone == 1) text-muted @endif">
					<td class="fit-content border-right">
						<a class="btn btn-secondary btn-sm undo-battery-execution-button @if($chargeCycleEntry->undone == 1) disabled @endif" href="#" data-charge-cycle-id="{{ $chargeCycleEntry->id }}" data-toggle="tooltip" data-placement="left" title="{{ $__t('Undo charge cycle') }}">
							<i class="fas fa-undo"></i>
						</a>
					</td>
					<td>
						<span class="name-anchor @if($chargeCycleEntry->undone == 1) text-strike-through @endif">{{ FindObjectInArrayByPropertyValue($batteries, 'id', $chargeCycleEntry->battery_id)->name }}</span>
						@if($chargeCycleEntry->undone == 1)
						<br>
						{{ $__t('Undone on') . ' ' . $chargeCycleEntry->undone_timestamp }}
						<time class="timeago timeago-contextual" datetime="{{ $chargeCycleEntry->undone_timestamp }}"></time>
						@endif
					</td>
					<td>
						{{ $chargeCycleEntry->tracked_time }}
						<time class="timeago timeago-contextual" datetime="{{ $chargeCycleEntry->tracked_time }}"></time>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
