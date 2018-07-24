@extends('layout.default')

@section('title', $L('Habit tracking'))
@section('activeNav', 'habittracking')
@section('viewJsName', 'habittracking')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="habittracking-form" novalidate>

			<div class="form-group">
				<label for="habit_id">{{ $L('Habit') }}</label>
				<select class="form-control combobox" id="habit_id" name="habit_id" required>
					<option value=""></option>
					@foreach($habits as $habit)
						<option value="{{ $habit->id }}">{{ $habit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('You have to select a habit') }}</div>
			</div>

			@include('components.datetimepicker', array(
				'id' => 'tracked_time',
				'label' => 'Tracked time',
				'format' => 'YYYY-MM-DD HH:mm:ss',
				'initWithNow' => true,
				'limitEndToNow' => true,
				'limitStartToNow' => false,
				'invalidFeedback' => $L('This can only be before now')
			))

			@include('components.userpicker', array(
				'label' => 'Done by',
				'users' => $users,
				'nextInputSelector' => '#user_id',
				'prefillByUserId' => GROCY_USER_ID
			))

			<button id="save-habittracking-button" type="submit" class="btn btn-success">{{ $L('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.habitcard')
	</div>
</div>
@stop
