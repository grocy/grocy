@extends('layout.default')

@section('title', $__t('Batteries overview'))
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/batteriesjournal') }}">
				<i class="fas fa-file-alt"></i> {{ $__t('Journal') }}
			</a>
		</h1>
		<p id="info-due-batteries" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-batteries" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $__t('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $__t('All') }}</option>
			<option class="bg-warning" value="duesoon">{{ $__t('Due soon') }}</option>
			<option class="bg-danger" value="overdue">{{ $__t('Overdue') }}</option>
		</select>
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
				@foreach($current as $curentBatteryEntry)
				<tr id="battery-{{ $curentBatteryEntry->battery_id }}-row" class="@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-charge-cycle-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Track charge cycle of battery %s', FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name) }}"
							data-battery-id="{{ $curentBatteryEntry->battery_id }}"
							data-battery-name="{{ FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name }}">
							<i class="fas fa-fire"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item battery-name-cell" data-chore-id="{{ $curentBatteryEntry->battery_id }}" type="button" href="#">
									<i class="fas fa-info"></i> {{ $__t('Show battery details') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/batteriesjournal?battery=') }}{{ $curentBatteryEntry->battery_id }}">
									<i class="fas fa-file-alt"></i> {{ $__t('Journal for this battery') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/battery/') }}{{ $curentBatteryEntry->battery_id }}">
									<i class="fas fa-edit"></i> {{ $__t('Edit battery') }}
								</a>
							</div>
						</div>
					</td>
					<td class="battery-name-cell cursor-link" data-battery-id="{{ $curentBatteryEntry->battery_id }}">
						{{ FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name }}
					</td>
					<td>
						<span id="battery-{{ $curentBatteryEntry->battery_id }}-last-tracked-time">{{ $curentBatteryEntry->last_tracked_time }}</span>
						<time id="battery-{{ $curentBatteryEntry->battery_id }}-last-tracked-time-timeago" class="timeago timeago-contextual" datetime="{{ $curentBatteryEntry->last_tracked_time }}"></time>
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0)
							<span id="battery-{{ $curentBatteryEntry->battery_id }}-next-charge-time">{{ $curentBatteryEntry->next_estimated_charge_time }}</span>
							<time id="battery-{{ $curentBatteryEntry->battery_id }}-next-charge-time-timeago" class="timeago timeago-contextual" datetime="{{ $curentBatteryEntry->next_estimated_charge_time }}"></time>
						@else
							...
						@endif
					</td>
					<td class="d-none">
						"@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) overdue @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) duesoon @endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $curentBatteryEntry->battery_id)
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
