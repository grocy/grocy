@extends('layout.default')

@section('title', $__t('Tasks'))
@section('activeNav', 'tasks')
@section('viewJsName', 'tasks')

@push('pageScripts')
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/task/new') }}">
				<i class="fas fa-plus"></i> {{ $__t('Add') }}
			</a>
		</h1>
		<p id="info-due-tasks" data-status-filter="duesoon" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning status-filter-button responsive-button mr-2"></p>
		<p id="info-overdue-tasks" data-status-filter="overdue" class="btn btn-lg btn-danger status-filter-button responsive-button"></p>
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
	<div class="col-xs-12 col-md-6 col-xl-3 d-flex align-items-end">
		<div class="form-check">
			<input class="form-check-input" type="checkbox" id="show-done-tasks">
			<label class="form-check-label" for="show-done-tasks">
				{{ $__t('Show done tasks') }}
			</label>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="tasks-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Task') }}</th>
					<th>{{ $__t('Due') }}</th>
					<th class="d-none">Hidden category</th>
					<th>{{ $__t('Assigned to') }}</th>
					<th class="d-none">Hidden status</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($tasks as $task)
				<tr id="task-{{ $task->id }}-row" class="@if($task->done == 1) text-muted @endif @if(!empty($task->due_date) && $task->due_date < date('Y-m-d')) table-danger @elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content border-right">
						@if($task->done == 0)
						<a class="btn btn-success btn-sm do-task-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Mark task "%s" as completed', $task->name) }}"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}">
							<i class="fas fa-check"></i>
						</a>
						@else
						<a class="btn btn-secondary btn-sm undo-task-button" href="#" data-toggle="tooltip" data-placement="left" title="{{ $__t('Undo task "%s"', $task->name) }}"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}">
							<i class="fas fa-undo"></i>
						</a>
						@endif
						<a class="btn btn-sm btn-danger delete-task-button" href="#"
							data-task-id="{{ $task->id }}"
							data-task-name="{{ $task->name }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-info btn-sm" href="{{ $U('/task/') }}{{ $task->id }}">
							<i class="fas fa-edit"></i>
						</a>
					</td>
					<td id="task-{{ $task->id }}-name" class="@if($task->done == 1) text-strike-through @endif">
						{{ $task->name }}
					</td>
					<td>
						<span>{{ $task->due_date }}</span>
						<time class="timeago timeago-contextual" datetime="{{ $task->due_date }}"></time>
					</td>
					<td class="d-none">
						@if($task->category_id != null) <span>{{ FindObjectInArrayByPropertyValue($taskCategories, 'id', $task->category_id)->name }}</span> @else <span class="font-italic font-weight-light">{{ $__t('Uncategorized') }}</span>@endif
					</td>
					<td>
						@if($task->assigned_to_user_id != null) <span>{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $task->assigned_to_user_id)) }}</span> @endif
					</td>
					<td class="d-none">
						@if($task->done == 1) text-muted @endif @if(!empty($task->due_date) && $task->due_date < date('Y-m-d')) overdue @elseif(!empty($task->due_date) && $task->due_date < date('Y-m-d', strtotime("+$nextXDays days"))) duesoon @endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $task->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
