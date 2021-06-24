@extends($rootLayout)

@section('title', $__t('Chores overview'))
@section('activeNav', 'choresoverview')
@section('viewJsName', 'choresoverview')

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
					href="{{ $U('/choresjournal') }}">
					{{ $__t('Journal') }}
				</a>
			</div>
		</div>
		<div class="border-top border-bottom my-2 py-1">
			<div id="info-due-chores"
				data-status-filter="duesoon"
				data-next-x-days="{{ $nextXDays }}"
				class="warning-message status-filter-message responsive-message mr-2"></div>
			<div id="info-overdue-chores"
				data-status-filter="overdue"
				class="error-message status-filter-message responsive-button mr-2"></div>
			@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
			<div id="info-assigned-to-me-chores"
				data-user-filter="xx{{ GROCY_USER_ID }}xx"
				class="normal-message user-filter-message responsive-button"></div>
			@endif
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
	@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Assignment') }}</span>
			</div>
			<select class="custom-control custom-select"
				id="user-filter">
				<option></option>
				@foreach($users as $user)
				<option data-user-id="{{ $user->id }}"
					value="xx{{ $user->id }}xx">{{ $user->display_name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
</div>

<div class="row">
	<div class="col">
		<table id="chores-overview-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#chores-overview-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Chore') }}</th>
					<th>{{ $__t('Next estimated tracking') }}</th>
					<th>{{ $__t('Last tracked') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS) d-none @endif">{{ $__t('Assigned to') }}</th>
					<th class="d-none">Hidden status</th>
					<th class="d-none">Hidden assigned to user id</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($currentChores as $curentChoreEntry)
				<tr id="chore-{{ $curentChoreEntry->chore_id }}-row"
					class="@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s')) table-danger @elseif(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d H:i:s', strtotime('+' . $nextXDays . ' days')))
					table-warning
					@endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm track-chore-button permission-CHORE_TRACK_EXECUTION"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Track chore execution') }}"
							data-chore-id="{{ $curentChoreEntry->chore_id }}"
							data-chore-name="{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}">
							<i class="fas fa-play"></i>
						</a>
						<div class="dropdown d-inline-block">
							<button class="btn btn-sm btn-light text-secondary"
								type="button"
								data-toggle="dropdown">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu dropdown-menu-right">
								<a class="dropdown-item chore-name-cell"
									data-chore-id="{{ $curentChoreEntry->chore_id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Chore overview') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/choresjournal?embedded&chore=') }}{{ $curentChoreEntry->chore_id }}">
									<span class="dropdown-item-text">{{ $__t('Chore journal') }}</span>
								</a>
								<a class="dropdown-item permission-MASTER_DATA_EDIT"
									type="button"
									href="{{ $U('/chore/') }}{{ $curentChoreEntry->chore_id }}">
									<span class="dropdown-item-text">{{ $__t('Edit chore') }}</span>
								</a>
							</div>
						</div>
					</td>
					<td class="chore-name-cell cursor-link"
						data-chore-id="{{ $curentChoreEntry->chore_id }}">
						{{ FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY)
						<span id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time">{{ $curentChoreEntry->next_estimated_execution_time }}</span>
						<time id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-time-timeago"
							class="timeago timeago-contextual @if($curentChoreEntry->track_date_only == 1) timeago-date-only @endif"
							datetime="{{ $curentChoreEntry->next_estimated_execution_time }}"></time>
						@else
						<span>-</span>
						@endif
					</td>
					<td>
						<span id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time">{{ $curentChoreEntry->last_tracked_time }}</span>
						<time id="chore-{{ $curentChoreEntry->chore_id }}-last-tracked-time-timeago"
							class="timeago timeago-contextual @if($curentChoreEntry->track_date_only == 1) timeago-date-only @endif"
							datetime="{{ $curentChoreEntry->last_tracked_time }}"></time>
					</td>

					<td class="@if(!GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS) d-none @endif">
						<span id="chore-{{ $curentChoreEntry->chore_id }}-next-execution-assigned-user">
							@if(!empty($curentChoreEntry->next_execution_assigned_to_user_id))
							{{ FindObjectInArrayByPropertyValue($users, 'id', $curentChoreEntry->next_execution_assigned_to_user_id)->display_name }}
							@else
							<span>-</span>
							@endif
						</span>
					</td>
					<td id="chore-{{ $curentChoreEntry->chore_id }}-due-filter-column"
						class="d-none">
						@if(FindObjectInArrayByPropertyValue($chores, 'id', $curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d
							H:i:s'))
							overdue
							@elseif(FindObjectInArrayByPropertyValue($chores, 'id'
							,
							$curentChoreEntry->chore_id)->period_type !== \Grocy\Services\ChoresService::CHORE_PERIOD_TYPE_MANUALLY && $curentChoreEntry->next_estimated_execution_time < date('Y-m-d
								H:i:s',
								strtotime('+'
								.
								$nextXDays
								. ' days'
								)))
								duesoon
								@endif
								</td>
								<td
								class="d-none">
								@if(!empty($curentChoreEntry->next_execution_assigned_to_user_id))
								xx{{ $curentChoreEntry->next_execution_assigned_to_user_id }}xx
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

<div class="modal fade"
	id="choresoverview-chorecard-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				@include('components.chorecard')
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
