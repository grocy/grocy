@extends('layout.default')

@section('title', $L('Chores overview'))
@section('activeNav', 'choresoverview')
@section('viewJsName', 'choresoverview')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/choresjournal') }}">
				<i class="fas fa-file-alt"></i> {{ $L('Journal') }}
			</a>
		</h1>
		<p id="info-due-chores" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-chores" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $L('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $L('All') }}</option>
			<option class="bg-warning" value="duesoon">{{ $L('Due soon') }}</option>
			<option class="bg-danger" value="overdue">{{ $L('Overdue') }}</option>
		</select>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="chores-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Chore') }}</th>
					<th>{{ $L('Next estimated tracking') }}</th>
					<th>{{ $L('Last tracked') }}</th>
					<th class="d-none">Hidden status</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentChores as $curentChoreEntry)
				<tr id="chore-{{ $curentChoreEntry->chore_id }}-row" class="@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type === \Grocy\Services\ChoresService::CHORE_TYPE_DYNAMIC_REGULAR && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type === \Grocy\Services\ChoresService::CHORE_TYPE_DYNAMIC_REGULAR && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content">
						<a class="btn btn-success btn-sm track-chore-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $L('Track execution of chore #1', FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name) }}"
							data-chore-id="{{ $curentChoreEntry->chore_id }}"
							data-chore-name="{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}">
							<i class="fas fa-play"></i>
						</a>
						<a class="btn btn-info btn-sm" href="{{ $U('/choresjournal?chore=') }}{{ $curentChoreEntry->chore_id }}">
							<i class="fas fa-file-alt"></i>
						</a>
					</td>
					<td>
						{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type === \Grocy\Services\ChoresService::CHORE_TYPE_DYNAMIC_REGULAR)
							<span id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time">{{ $curentChoreEntry->next_estimated_execution_time }}</span>
							<time id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time-timeago" class="timeago timeago-contextual" datetime="{{ $curentChoreEntry->next_estimated_execution_time }}"></time>
						@else
							...
						@endif
					</td>
					<td>
						<span id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time">{{ $curentChoreEntry->last_tracked_time }}</span>
						<time id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time-timeago" class="timeago timeago-contextual" datetime="{{ $curentChoreEntry->last_tracked_time }}"></time>
					</td>
					<td class="d-none">
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type === \Grocy\Services\ChoresService::CHORE_TYPE_DYNAMIC_REGULAR && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) overdue @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type === \Grocy\Services\ChoresService::CHORE_TYPE_DYNAMIC_REGULAR && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) duesoon @endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
