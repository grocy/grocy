@extends('layout.default')

@section('title', $__t('Chores overview'))
@section('activeNav', 'choresoverview')
@section('viewJsName', 'choresoverview')

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="related-links">
				<a class="btn btn-outline-dark responsive-button" href="{{ $U('/choresjournal') }}">
					{{ $__t('Journal') }}
				</a>
			</div>
		</div>
		<hr>
		<p id="info-due-chores" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="warning-message status-filter-message responsive-message mr-2"></p>
		<p id="info-overdue-chores" data-status-filter="overdue" class="error-message status-filter-message responsive-button mr-2"></p>
		@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
		<p id="info-assigned-to-me-chores" data-user-filter="xx{{ GROCY_USER_ID }}xx" class="normal-message user-filter-message responsive-button"></p>
		@endif
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3 d-flex align-items-end">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="status-filter">{{ $__t('Status') }}</label>
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
	@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="user-filter">{{ $__t('Assignment') }}</label>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-filter"></i></span>
			</div>
			<select class="form-control" id="user-filter">
				<option></option>
				@foreach($users as $user)
				<option data-user-id="{{ $user->id }}" value="xx{{ $user->id }}xx">{{ $user->display_name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
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
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<th>{{ $__t('Assigned to') }}</th>
					@endif
					<th class="d-none">Hidden status</th>
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<th class="d-none">Hidden assigned to user id</th>
					@endif

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentChores as $curentChoreEntry)
				<tr id="chore-{{ $curentChoreEntry->chore_id }}-row" class="@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
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
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item chore-name-cell" data-chore-id="{{ $curentChoreEntry->chore_id }}" type="button" href="#">
									<span class="dropdown-item-icon"><i class="fas fa-info"></i></span> <span class="dropdown-item-text">{{ $__t('Show chore details') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/choresjournal?chore=') }}{{ $curentChoreEntry->chore_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-file-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Journal for this chore') }}</span>
								</a>
								<a class="dropdown-item" type="button" href="{{ $U('/chore/') }}{{ $curentChoreEntry->chore_id }}">
									<span class="dropdown-item-icon"><i class="fas fa-edit"></i></span> <span class="dropdown-item-text">{{ $__t('Edit chore') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td class="chore-name-cell cursor-link" data-chore-id="{{ $curentChoreEntry->chore_id }}">
						{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY)
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
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<td>
						<span id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-assigned-user">
						@if(!empty($curentChoreEntry->next_execution_assigned_to_user_id))
							{{ FindObjectInArrayByPropertyValue($users, 'id', $curentChoreEntry->next_execution_assigned_to_user_id)->display_name }}
						@else
							...
						@endif
						</span>
					</td>
					@endif
					<td id="chore-{{ $curentChoreEntry->chore_id }}-due-filter-column" class="d-none">
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) overdue @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) duesoon @endif
					</td>
					@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
					<td class="d-none">
						@if(!empty($curentChoreEntry->next_execution_assigned_to_user_id))
							xx{{ $curentChoreEntry->next_execution_assigned_to_user_id }}xx
						@endif
					</td>
					@endif

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
