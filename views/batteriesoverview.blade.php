@extends('layout.default')

@section('title', $L('Batteries overview'))
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@section('content')
<h1 class="page-header">@yield('title')</h1>

<div class="container-fluid">
	<div class="row">
		<p class="btn btn-lg btn-warning no-real-button responsive-button">{{ $L('#1 batteries are due to be charged within the next #2 days', $countDueNextXDays, $nextXDays) }}</p>
		<p class="btn btn-lg btn-danger no-real-button responsive-button">{{ $L('#1 batteries are overdue to be charged', $countOverdue) }}</p>
	</div>
</div>

<div class="discrete-content-separator-2x"></div>

<div class="table-responsive">
	<table id="batteries-overview-table" class="table table-striped">
		<thead>
			<tr>
				<th>{{ $L('Battery') }}</th>
				<th>{{ $L('Last charged') }}</th>
				<th>{{ $L('Next planned charge cycle') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($current as $curentBatteryEntry)
			<tr class="@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0 && $nextChargeTimes[$curentBatteryEntry->battery_id] < date('Y-m-d H:i:s')) error-bg @endif">
				<td>
					{{ FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->name }}
				</td>
				<td>
					{{ $curentBatteryEntry->last_tracked_time }}
					<time class="timeago timeago-contextual" datetime="{{ $curentBatteryEntry->last_tracked_time }}"></time>
				</td>
				<td>
					@if(FindObjectInArrayByPropertyValue($batteries, 'id', $curentBatteryEntry->battery_id)->charge_interval_days > 0)
						{{ $nextChargeTimes[$curentBatteryEntry->battery_id] }}
						<time class="timeago timeago-contextual" datetime="{{ $nextChargeTimes[$curentBatteryEntry->battery_id] }}"></time>
					@else
						...
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@stop
