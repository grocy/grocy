@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit task category'))
@else
	@section('title', $L('Create task category'))
@endif

@section('viewJsName', 'taskcategoryform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $category->id }};</script>
		@endif

		<form id="task-category-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $category->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $category->description }}@endif</textarea>
			</div>

			<button id="save-task-category-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
