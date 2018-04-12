@extends('layout.default')

@if($mode == 'edit')
	@section('title', 'Edit habit')
@else
	@section('title', 'Create habit')
@endif

@section('viewJsName', 'habitform')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2 main">

	<h1 class="page-header">@yield('title')</h1>

	<script>Grocy.EditMode = '{{ $mode }}';</script>

	@if($mode == 'edit')
		<script>Grocy.EditObjectId = {{ $habit->id }};</script>
	@endif

	<form id="habit-form">

		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $habit->name }}@endif">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $habit->description }}@endif</textarea>
		</div>

		<div class="form-group">
			<label for="period_type">Period type</label>
			<select required class="form-control input-group-habit-period-type" id="period_type" name="period_type">
				@foreach($periodTypes as $periodType)
					<option @if($mode == 'edit' && $periodType == $habit->period_type) selected="selected" @endif value="{{ $periodType }}">{{ $periodType }}</option>
				@endforeach
			</select>
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="period_days">Period days</label>
			<input type="number" class="form-control input-group-habit-period-type" id="period_days" name="period_days" value="@if($mode == 'edit'){{ $habit->period_days }}@endif">
			<div class="help-block with-errors"></div>
		</div>

		<p id="habit-period-type-info" class="help-block text-muted"></p>

		<button id="save-habit-button" type="submit" class="btn btn-default">Save</button>

	</form>

</div>
@stop
