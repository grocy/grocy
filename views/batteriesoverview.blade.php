@extends($rootLayout)

@section('title', $__t('Batteries overview'))
@section('activeNav', 'batteriesoverview')
@section('viewJsName', 'batteriesoverview')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fas fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-outline-dark responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/batteriesjournal') }}">
					{{ $__t('Journal') }}
				</a>
			</div>
		</div>
		<div class="border-top border-bottom my-2 py-1">
			<div id="info-due-batteries"
				data-status-filter="duesoon"
				data-next-x-days="{{ $nextXDays }}"
				class="warning-message status-filter-message responsive-button mr-2"></div>
			<div id="info-overdue-batteries"
				data-status-filter="overdue"
				class="error-message status-filter-message responsive-button"></div>
			<div class="float-right">
				<a class="btn btn-sm btn-outline-info d-md-none mt-1"
					data-toggle="collapse"
					href="#table-filter-row"
					role="button">
					<i class="fas fa-filter"></i>
				</a>
				<a id="clear-filter-button"
					class="btn btn-sm btn-outline-info mt-1"
					href="#">
					{{ $__t('Clear filter') }}
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row collapse d-md-flex"
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
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="status-filter">
				<option value="all">{{ $__t('All') }}</option>
				<option value="duesoon">{{ $__t('Due soon') }}</option>
				<option value="overdue">{{ $__t('Overdue') }}</option>
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="batteries-overview-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#batteries-overview-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Battery') }}</th>
					<th>{{ $__t('Used in') }}</th>
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
				<tr id="battery-{{ $currentBatteryEntry->battery_id }}-row"
					class="@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d H:i:s', strtotime('+' . $nextXDays . ' days')))
					table-warning
					@endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-charge-cycle-button permission-BATTERIES_TRACK_CHARGE_CYCLE"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Track charge cycle') }}"
							data-battery-id="{{ $currentBatteryEntry->battery_id }}"
							data-battery-name="{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name }}">
							<i class="fas fa-car-battery"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item battery-name-cell"
									data-battery-id="{{ $currentBatteryEntry->battery_id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Battery overview') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/batteriesjournal?embedded&battery=') }}{{ $currentBatteryEntry->battery_id }}">
									<span class="dropdown-item-text">{{ $__t('Battery journal') }}</span>
								</a>
								<a class="dropdown-item permission-MASTER_DATA_EDIT show-as-dialog-link"
									type="button"
									href="{{ $U('/battery/') }}{{ $currentBatteryEntry->battery_id }}?embedded">
									<span class="dropdown-item-text">{{ $__t('Edit battery') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td class="battery-name-cell cursor-link"
						data-battery-id="{{ $currentBatteryEntry->battery_id }}">
						{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name }}
					</td>
					<td class="fit-content">
						{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->used_in }}
					</td>
					<td>
						<span id="battery-{{ $currentBatteryEntry->battery_id }}-last-tracked-time">{{ $currentBatteryEntry->last_tracked_time }}</span>
						<time id="battery-{{ $currentBatteryEntry->battery_id }}-last-tracked-time-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $currentBatteryEntry->last_tracked_time }}"></time>
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0)
						<span id="battery-{{ $currentBatteryEntry->battery_id }}-next-charge-time">{{ $currentBatteryEntry->next_estimated_charge_time }}</span>
						<time id="battery-{{ $currentBatteryEntry->battery_id }}-next-charge-time-timeago"
							class="timeago timeago-contextual"
							datetime="{{ $currentBatteryEntry->next_estimated_charge_time }}"></time>
						@else
						...
						@endif
					</td>
					<td class="d-none">
						"@if(FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d
							H:i:s'))
							overdue
							@elseif(FindObjectInArrayByPropertyValue($batteries, 'id'
							,
							$currentBatteryEntry->battery_id)->charge_interval_days > 0 && $currentBatteryEntry->next_estimated_charge_time < date('Y-m-d
								H:i:s',
								strtotime('+'
								.
								$nextXDays
								. ' days'
								)))
								duesoon
								@endif
								</td>
								@include('components.userfields_tbody',
								array( 'userfields'=> $userfields,
								'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentBatteryEntry->battery_id)
								))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade"
	id="batteriesoverview-batterycard-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.batterycard')
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
