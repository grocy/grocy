@extends('layout.default')

@section('title', $L('Tasks'))
@section('activeNav', 'tasks')
@section('viewJsName', 'tasks')

@push('pageScripts')
	<script src="{{ $U('/node_modules/jquery-ui-dist/jquery-ui.min.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h1>@yield('title')</h1>
		<p id="info-due-tasks" data-next-x-days="{{ $nextXDays }}" class="btn btn-lg btn-warning no-real-button responsive-button mr-2"></p>
		<p id="info-overdue-tasks" class="btn btn-lg btn-danger no-real-button responsive-button"></p>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="tasks-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Aufgabe') }}</th>
					<th>{{ $L('Due') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($tasks as $task)
				<tr id="task-{{ $task->id }}-row" class="@if($task->due < date('Y-m-d H:i:s')) table-danger @elseif($task->due < date('Y-m-d H:i:s', strtotime("+$nextXDays days"))) table-warning @endif">
					<td class="fit-content">
						<a class="btn btn-success btn-sm do-task-button" href="#" data-toggle="tooltip" title="{{ $L('Mark task "#1" as completed', $task->name) }}"
							data-task-id="{{ $task->id }}">
							<i class="fas fa-check"></i>
						</a>
						<a class="btn btn-success btn-sm start-task-button" href="#" data-toggle="tooltip" title="{{ $L('Start task "#1"', $task->name) }}"
							data-task-id="{{ $task->id }}">
							<i class="fas fa-play"></i>
						</a>
						<a class="btn btn-info btn-sm" href="{{ $U('/task/') }}{{ $task->id }}">
							<i class="fas fa-edit"></i>
						</a>
					</td>
					<td>
						{{ $task->name }}
					</td>
					<td>
						<span>{{ $task->due }}</span>
						<time class="timeago timeago-contextual" datetime="{{ $task->due }}"></time>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
