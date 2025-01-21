@php require_frontend_packages(['datatables', 'animatecss']); @endphp

@extends('layout.default')

@section('title', $__t('Batteries overview'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
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
			<div id="info-overdue-batteries"
				data-status-filter="overdue"
				class="error-message status-filter-message responsive-button mr-2"></div>
			<div id="info-due-today-batteries"
				data-status-filter="duetoday"
				class="normal-message status-filter-message responsive-button mr-2"></div>
			<div id="info-due-soon-batteries"
				data-status-filter="duesoon"
				data-next-x-days="{{ $nextXDays }}"
				class="warning-message status-filter-message responsive-button @if($nextXDays == 0) d-none @endif"></div>
			<div class="float-right mt-1 @if($embedded) pr-5 @endif">
				<a class="btn btn-sm btn-outline-info d-md-none"
					data-toggle="collapse"
					href="#table-filter-row"
					role="button">
					<i class="fa-solid fa-filter"></i>
				</a>
				<button id="clear-filter-button"
					class="btn btn-sm btn-outline-info"
					data-toggle="tooltip"
					title="{{ $__t('Clear filter') }}">
					<i class="fa-solid fa-filter-circle-xmark"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-search"></i></span>
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
				<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="status-filter">
				<option value="all">{{ $__t('All') }}</option>
				<option value="overdue">{{ $__t('Overdue') }}</option>
				<option value="duetoday">{{ $__t('Due today') }}</option>
				@if($nextXDays > 0)
				<option value="duesoon">{{ $__t('Due soon') }}</option>
				@endif
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
							title="{{ $__t('Table options') }}"
							data-table-selector="#batteries-overview-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Battery') }}</th>
					<th class="allow-grouping">{{ $__t('Used in') }}</th>
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
					class="@if($currentBatteryEntry->due_type == 'overdue') table-danger @elseif($currentBatteryEntry->due_type == 'duetoday') table-info @elseif($currentBatteryEntry->due_type == 'duesoon') table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-charge-cycle-button permission-BATTERIES_TRACK_CHARGE_CYCLE"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Track charge cycle') }}"
							data-battery-id="{{ $currentBatteryEntry->battery_id }}"
							data-battery-name="{{ FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name }}">
							<i class="fa-solid fa-car-battery"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item batterycard-trigger"
									data-battery-id="{{ $currentBatteryEntry->battery_id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Battery overview') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/batteriesjournal?embedded&battery=') }}{{ $currentBatteryEntry->battery_id }}"
									data-dialog-type="table">
									<span class="dropdown-item-text">{{ $__t('Battery journal') }}</span>
								</a>
								<a class="dropdown-item permission-MASTER_DATA_EDIT show-as-dialog-link"
									type="button"
									href="{{ $U('/battery/') }}{{ $currentBatteryEntry->battery_id }}?embedded">
									<span class="dropdown-item-text">{{ $__t('Edit battery') }}</span>
								</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/battery/' . $currentBatteryEntry->battery_id . '/grocycode?download=true') }}">
									{!! str_replace('grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Download %s Grocycode', $__t('Battery'))) !!}
								</a>
								@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
								<a class="dropdown-item battery-grocycode-label-print"
									data-battery-id="{{ $currentBatteryEntry->battery_id }}"
									type="button"
									href="#">
									{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Print %s Grocycode on label printer', $__t('Battery'))) !!}
								</a>
								@endif
							</div>
						</div>
					</td>
					<td class="batterycard-trigger cursor-link"
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
						{{ $currentBatteryEntry->due_type }}
						@if($currentBatteryEntry->due_type == 'duetoday')
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

@include('components.batterycard', [
'asModal' => true
])
@stop
