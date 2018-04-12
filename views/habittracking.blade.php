@extends('layout.default')

@section('title', 'Habit tracking')
@section('activeNav', 'habittracking')
@section('viewJsName', 'habittracking')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-3 col-md-offset-2 main">

	<h1 class="page-header">Habit tracking</h1>

	<form id="habittracking-form">

		<div class="form-group">
			<label for="habit_id">Habit</label>
			<select class="form-control combobox" id="habit_id" name="habit_id" required>
				<option value=""></option>
				@foreach($habits as $habit)
					<option value="{{ $habit->id }}">{{ $habit->name }}</option>
				@endforeach
			</select>
			<div id="product-error" class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="tracked_time">Tracked time</label>
			<div class="input-group date datetimepicker">
				<input type="text" class="form-control" id="tracked_time" name="tracked_time" required >
				<span class="input-group-addon">
					<span class="fa fa-calendar"></span>
				</span>
			</div>
			<div class="help-block with-errors"></div>
		</div>

		<button id="save-habittracking-button" type="submit" class="btn btn-default">OK</button>

	</form>

</div>

<div class="col-sm-6 col-md-5 col-lg-3 main well">
	<h3>Habit overview <strong><span id="selected-habit-name"></span></strong></h3>

	<p>
		<strong>Tracked count:</strong> <span id="selected-habit-tracked-count"></span><br>
		<strong>Last tracked:</strong> <span id="selected-habit-last-tracked"></span> <time id="selected-habit-last-tracked-timeago" class="timeago timeago-contextual"></time><br>
	</p>
</div>
@stop
