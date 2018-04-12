@extends('layout.default')

@section('title', 'Batteries overview')
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">Batteries overview</h1>

	<div class="table-responsive">
		<table id="batteries-overview-table" class="table table-striped">
			<thead>
				<tr>
					<th>Battery</th>
					<th>Last charged</th>
					<th>Next planned charge cycle</th>
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

</div>
@stop
