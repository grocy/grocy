@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit task'))
@else
@section('title', $__t('Create task'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $task->id }};
		</script>
		@endif

		<form id="task-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $task->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control"
					rows="4"
					id="description"
					name="description">@if($mode == 'edit'){{ $task->description }}@endif</textarea>
			</div>

			@php
			$initialDueDate = null;
			if ($mode == 'edit' && !empty($task->due_date))
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
			'invalidFeedback' => $__t('A due date is required'),
			'nextInputSelector' => 'category_id',
			'additionalGroupCssClasses' => 'date-only-datetimepicker',
			'isRequired' => false
			))

			<div class="form-group">
				<label for="category_id">{{ $__t('Category') }}</label>
				<select class="custom-control custom-select"
					id="category_id"
					name="category_id">
					<option></option>
					@foreach($taskCategories as $taskCategory)
					<option @if($mode=='edit'
						&&
						$taskCategory->id == $task->category_id) selected="selected" @endif value="{{ $taskCategory->id }}">{{ $taskCategory->name }}</option>
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

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'tasks'
			))

			@if($mode == 'edit')
			<button class="btn btn-success save-task-button">{{ $__t('Save') }}</button>
			@else
			<button class="btn btn-success save-task-button">{{ $__t('Save & close') }}</button>
			<button class="btn btn-primary save-task-button add-another">{{ $__t('Save & add another task') }}</button>
			@endif
		</form>
	</div>
</div>
@stop
