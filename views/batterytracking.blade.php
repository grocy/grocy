@extends('layout.default')

@section('title', $L('Battery tracking'))
@section('activeNav', 'batterytracking')
@section('viewJsName', 'batterytracking')

@section('content')
<div class="col-lg-4 col-xs-12">
	<h1 class="page-header">@yield('title')</h1>

	<form id="batterytracking-form">

		<div class="form-group">
			<label for="battery_id">{{ $L('Battery') }}</label>
			<select class="form-control combobox" id="battery_id" name="battery_id" required>
				<option value=""></option>
				@foreach($batteries as $battery)
					<option value="{{ $battery->id }}">{{ $battery->name }}</option>
				@endforeach
			</select>
			<div id="battery-error" class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="tracked_time">{{ $L('Tracked time') }}</label>
			<div class="input-group date datetimepicker">
				<input type="text" class="form-control" id="tracked_time" name="tracked_time" required >
				<span class="input-group-addon">
					<span class="fa fa-calendar"></span>
				</span>
			</div>
			<div class="help-block with-errors"></div>
		</div>

		<button id="save-batterytracking-button" type="submit" class="btn btn-default">{{ $L('OK') }}</button>

	</form>
</div>

<div class="col-lg-4 col-xs-12">
	@include('components.batterycard')
</div>
@stop
