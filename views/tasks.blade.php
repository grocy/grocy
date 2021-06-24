@extends($rootLayout)

@section('title', $__t('Tasks'))
@section('activeNav', 'tasks')
@section('viewJsName', 'tasks')

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
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100 m-1 mt-md-0 mb-md-0 float-right"
				id="related-links">
				<a class="btn btn-primary responsive-button show-as-dialog-link"
					href="{{ $U('/task/new?embedded') }}">
					{{ $__t('Add') }}
				</a>
			</div>
		</div>
		<div class="border-top border-bottom my-2 py-1">
			<div id="info-due-tasks"
				data-status-filter="duesoon"
				data-next-x-days="{{ $nextXDays }}"
				class="warning-message status-filter-message responsive-button mr-2"></div>
			<div id="info-overdue-tasks"
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
	<div class="col-12 col-md-6 col-xl-3">
		<div class="form-check custom-control custom-checkbox">
			<input class="form-check-input custom-control-input"
				type="checkbox"
				id="show-done-tasks">
			<label class="form-check-label custom-control-label"
				for="show-done-tasks">
				{{ $__t('Show done tasks') }}
			</label>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="tasks-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#tasks-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('Task') }}</th>
					<th>{{ $__t('Due') }}</th>
					<th>{{ $__t('Category') }}</th>
					<th>{{ $__t('Assigned to') }}</th>
					<th class="d-none">Hidden status</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($tasks as $task)
				<tr id="task-{{ $task->id }}-row"
					class="@if($task->done == 1) text-muted @endif @if(!empty($task->due_date) && $task->due_date < date('Y-m-d')) table-danger @elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d', strtotime('+' . $nextXDays . ' days')))
					table-warning
					@endif">
					<td class="fit-content border-right">
						@if($task->done == 0)
						<a class="btn btn-success btn-sm do-task-button"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark task as completed') }}"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}">
							<i class="fas fa-check"></i>
						</a>
						@else
						<a class="btn btn-secondary btn-sm undo-task-button"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Undo task', $task->name) }}"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}">
							<i class="fas fa-undo"></i>
						</a>
						@endif
						<a class="btn btn-info btn-sm show-as-dialog-link"
							href="{{ $U('/task/') }}{{ $task->id }}?embedded"
							data-toggle="tooltip"
							title="{{ $__t('Edit this item') }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger delete-task-button"
							href="#"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td id="task-{{ $task->id }}-name"
						class="@if($task->done == 1) text-strike-through @endif">
						{{ $task->name }}
					</td>
					<td>
						<span>{{ $task->due_date }}</span>
						<time class="timeago timeago-contextual"
							datetime="{{ $task->due_date }}"></time>
					</td>
					<td>
						@if($task->category_id != null) <span>{{ FindObjectInArrayByPropertyValue($taskCategories, 'id', $task->category_id)->name }}</span> @else <span class="font-italic font-weight-light">{{ $__t('Uncategorized') }}</span>@endif
					</td>
					<td>
						@if($task->assigned_to_user_id != null) <span>{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $task->assigned_to_user_id)) }}</span> @endif
					</td>
					<td class="d-none">
						@if($task->done == 1) text-muted @endif @if(!empty($task->due_date) && $task->due_date < date('Y-m-d'))
							overdue
							@elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d',
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
								'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $task->id)
								))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
