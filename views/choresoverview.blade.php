@extends('layout.default')

@section('title', $__t('Chores overview'))
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
				<i class="fas fa-file-alt"></i> {{ $__t('Journal') }}
			</a>
		</h1>
		<p id="info-due-chores" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-chores" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
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
		<table id="chores-overview-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Chore') }}</th>
					<th>{{ $__t('Next estimated tracking') }}</th>
					<th>{{ $__t('Last tracked') }}</th>
					<th class="d-none">Hidden status</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentChores as $curentChoreEntry)
				<tr id="chore-{{ $curentChoreEntry->chore_id }}-row" class="@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-chore-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Track execution of chore %s', FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name) }}"
							data-chore-id="{{ $curentChoreEntry->chore_id }}"
							data-chore-name="{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}">
							<i class="fas fa-play"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary" type="button" data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item chore-name-cell" data-chore-id="{{ $curentChoreEntry->chore_id }}" type="button" href="#">
									<i class="fas fa-info"></i> {{ $__t('Show chore details') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/choresjournal?chore=') }}{{ $curentChoreEntry->chore_id }}">
									<i class="fas fa-file-alt"></i> {{ $__t('Journal for this chore') }}
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/chore/') }}{{ $curentChoreEntry->chore_id }}">
									<i class="fas fa-edit"></i> {{ $__t('Edit chore') }}
								</a>
							</div>
						</div>
					</td>
					<td class="chore-name-cell cursor-link" data-chore-id="{{ $curentChoreEntry->chore_id }}">
						{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_TYPE_MANUALLY)
							<span id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time">{{ $curentChoreEntry->next_estimated_execution_time }}</span>
							<time id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time-timeago" class="timeago timeago-contextual @if($curentChoreEntry->track_date_only == 1) timeago-date-only @endif" datetime="{{ $curentChoreEntry->next_estimated_execution_time }}"></time>
						@else
							...
						@endif
					</td>
					<td>
						<span id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time">{{ $curentChoreEntry->last_tracked_time }}</span>
						<time id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time-timeago" class="timeago timeago-contextual @if($curentChoreEntry->track_date_only == 1) timeago-date-only @endif" datetime="{{ $curentChoreEntry->last_tracked_time }}"></time>
					</td>
					<td class="d-none">
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) overdue @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) duesoon @endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $curentChoreEntry->chore_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="choresoverview-chorecard-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.chorecard')
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
