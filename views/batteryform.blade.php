@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit battery'))
@else
	@section('title', $L('Create battery'))
@endif

@section('viewJsName', 'batteryform')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2">

	<h1 class="page-header">@yield('title')</h1>

	<script>Grocy.EditMode = '{{ $mode }}';</script>

	@if($mode == 'edit')
		<script>Grocy.EditObjectId = {{ $battery->id }}</script>
	@endif

	<form id="battery-form">

		<div class="form-group">
			<label for="name">{{ $L('Name') }}</label>
			<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $battery->name }}@endif">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">{{ $L('Description') }}</label>
			<input type="text" class="form-control" id="description" name="description" value="@if($mode == 'edit'){{ $battery->description }}@endif">
		</div>

		<div class="form-group">
			<label for="name">{{ $L('Used in') }}</label>
			<input type="text" class="form-control" id="used_in" name="used_in" value="@if($mode == 'edit'){{ $battery->used_in }}@endif">
		</div>

		<button id="save-battery-button" type="submit" class="btn btn-default">{{ $L('Save') }}</button>

	</form>

</div>
@stop
