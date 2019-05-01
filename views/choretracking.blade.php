@extends('layout.default')

@section('title', $__t('Chore tracking'))
@section('activeNav', 'choretracking')
@section('viewJsName', 'choretracking')

@section('content')
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-4 pb-3">
		<h1>@yield('title')</h1>

		<form id="choretracking-form" novalidate>

			<div class="form-group">
				<label for="chore_id">{{ $__t('Chore') }}</label>
				<select class="form-control combobox" id="chore_id" name="chore_id" required>
					<option value=""></option>
					@foreach($chores as $chore)
						<option value="{{ $chore->id }}">{{ $chore->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('You have to select a chore') }}</div>
			</div>

			@include('components.datetimepicker', array(
				'id' => 'tracked_time',
				'label' => 'Tracked time',
				'format' => 'YYYY-MM-DD HH:mm:ss',
				'initWithNow' => true,
				'limitEndToNow' => true,
				'limitStartToNow' => false,
				'invalidFeedback' => $__t('This can only be before now')
			))

			@include('components.userpicker', array(
				'label' => 'Done by',
				'users' => $users,
				'nextInputSelector' => '#user_id',
				'prefillByUserId' => GROCY_USER_ID
			))

			<button id="save-choretracking-button" class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="col-xs-12 col-md-6 col-xl-4">
		@include('components.chorecard')
	</div>
</div>
@stop
