@extends('layout.default')

@section('title', $__t('Batteries overview'))
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-dark responsive-button" href="{{ $U('/batteriesjournal') }}">
				 {{ $__t('Journal') }}
				</a>
			</div>
		</div>
		<hr>
		<p id="info-due-batteries" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="warning-message status-filter-message responsive-button mr-2"></p>
		<p id="info-overdue-batteries" data-status-filter="overdue" class="error-message status-filter-message responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
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
			<select class="form-control" id="status-filter">
				<option value="all">{{ $__t('All') }}</option>
				<option value="duesoon">{{ $__t('Due soon') }}</option>
				<option value="overdue">{{ $__t('Overdue') }}</option>
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="batteries-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Battery') }}</th>
					<th>{{ $__t('Last charged') }}</th>
					<th>{{ $__t('Next planned charge cycle') }}</th>
					<th class="d-none">Hidden status</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))
					
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($current as $currentBatteryEntry)
				<tr id="battery-{{ $currentBatteryEntry->battery_id }}-row" class="@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-charge-cycle-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Track charge cycle of battery %s', FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name) }}"
							data-battery-id="{{ $currentBatteryEntry->battery_id }}"
							data-battery-name="{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name }}">
							<i class="fas fa-fire"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item battery-name-cell" data-battery-id="{{ $currentBatteryEntry->battery_id }}" type="button" href="#">
									<span class="dropdown-item-icon"><i class="fas fa-info"></i></span> <span class="dropdown-item-text">{{ $__t('Show battery details') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/batteriesjournal?battery=') }}{{ $currentBatteryEntry->battery_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-file-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Journal for this battery') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/battery/') }}{{ $currentBatteryEntry->battery_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-edit"></i></span> <span class="dropdown-item-text">{{ $__t('Edit battery') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td class="battery-name-cell cursor-link" data-battery-id="{{ $currentBatteryEntry->battery_id }}">
						{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name }}
					</td>
					<td>
						<span id="battery-{{ $currentBatteryEntry->battery_id }}-last-tracked-time">{{ $currentBatteryEntry->last_tracked_time }}</span>
						<time id="battery-{{ $currentBatteryEntry->battery_id }}-last-tracked-time-timeago" class="timeago timeago-contextual" datetime="{{ $currentBatteryEntry->last_tracked_time }}"></time>
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0)
							<span id="battery-{{ $currentBatteryEntry->battery_id }}-next-charge-time">{{ $currentBatteryEntry->next_estimated_charge_time }}</span>
							<time id="battery-{{ $currentBatteryEntry->battery_id }}-next-charge-time-timeago" class="timeago timeago-contextual" datetime="{{ $currentBatteryEntry->next_estimated_charge_time }}"></time>
						@else
							...
						@endif
					</td>
					<td class="d-none">
						"@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) overdue @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) duesoon @endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentBatteryEntry->battery_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="batteriesoverview-batterycard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.batterycard')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
