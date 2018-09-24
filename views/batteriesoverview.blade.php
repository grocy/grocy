@extends('layout.default')

@section('title', $L('Batteries overview'))
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1>
		<p id="info-due-batteries" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-batteries" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $L('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $L('All') }}</option>
			<option class="bg-warning" value="duesoon">{{ $L('Due soon') }}</option>
			<option class="bg-danger" value="overdue">{{ $L('Overdue') }}</option>
		</select>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="batteries-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Battery') }}</th>
					<th>{{ $L('Last charged') }}</th>
					<th>{{ $L('Next planned charge cycle') }}</th>
					<th class="d-none">Hidden status</th>
				</tr>
			</thead>
			<tbody>
				@foreach($current as $curentBatteryEntry)
				<tr id="battery-{{ $curentBatteryEntry->battery_id }}-row" class="@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $curentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content">
						<a class="btn btn-success btn-sm track-charge-cycle-button" href="#" data-toggle="tooltip" title="{{ $L('Track charge cycle of battery #1', FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name) }}"
							data-battery-id="{{ $curentBatteryEntry->battery_id }}"
							data-battery-name="{{ FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name }}">
							<i class="fas fa-fire"></i>
						</a>
					</td>
					<td>
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
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
