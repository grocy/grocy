@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit task'))
@else
	@section('title', $L('Create task'))
@endif

@section('viewJsName', 'taskform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $task->id }};</script>
		@endif

		<form id="task-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $task->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $task->description }}@endif</textarea>
			</div>

			@php
			$initialDueDate = null;
			if ($mode == 'edit')
			{
				$initialDueDate = date('Y-m-d', strtotime($task->due_date));
			}
			@endphp
			@include('components.datetimepicker', array(
				'id' => 'due_date',
				'label' => 'Due',
				'format' => 'YYYY-MM-DD',
				'initWithNow' => false,
				'initialValue' => $initialDueDate,
				'limitEndToNow' => false,
				'limitStartToNow' => false,
				'invalidFeedback' => $L('A due date is required'),
				'nextInputSelector' => 'category_id',
				'additionalCssClasses' => 'date-only-datetimepicker',
				'isRequired' => false
			))

			<div class="form-group">
				<label for="category_id">{{ $L('Category') }}</label>
				<select class="form-control" id="category_id" name="category_id">
					<option></option>
					@foreach($taskCategories as $taskCategory)
						<option @if($mode == 'edit' && $taskCategory->id == $task->category_id) selected="selected" @endif value="{{ $taskCategory->id }}">{{ $taskCategory->name }}</option>
					@endforeach
				</select>
			</div>

			@php
			$initUserId = GROCY_USER_ID;
			if ($mode == 'edit')
			{
				$initUserId = $task->assigned_to_user_id;
			}
			@endphp
			@include('components.userpicker', array(
				'label' => 'Assigned to',
				'users' => $users,
				'prefillByUserId' => $initUserId
			))

			<button id="save-task-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
