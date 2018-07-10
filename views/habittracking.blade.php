@extends('layout.default')

@section('title', $L('Habit tracking'))
@section('activeNav', 'habittracking')
@section('viewJsName', 'habittracking')

@section('content')
<div class="row">
	<div class="col-lg-4 col-xs-12">
		<h1>@yield('title')</h1>

		<form id="habittracking-form">

			<div class="form-group">
				<label for="habit_id">{{ $L('Habit') }}</label>
				<select class="form-control combobox" id="habit_id" name="habit_id" required>
					<option value=""></option>
					@foreach($habits as $habit)
						<option value="{{ $habit->id }}">{{ $habit->name }}</option>
					@endforeach
				</select>
				<div id="product-error" class="invalid-feedback"></div>
			</div>

			@include('components.datetimepicker', array(
				'id' => 'tracked_time',
				'label' => 'Tracked time'
			))

			<button id="save-habittracking-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-lg-4 col-xs-12">
		@include('components.habitcard')
	</div>
</div>
@stop
