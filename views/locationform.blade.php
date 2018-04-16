@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit location'))
@else
	@section('title', $L('Create location'))
@endif

@section('viewJsName', 'locationform')

@section('content')
<div class="col-sm-3 col-sm-offset-3 col-md-4 col-md-offset-2">

	<h1 class="page-header">@yield('title')</h1>

	<script>Grocy.EditMode = '{{ $mode }}';</script>

	@if($mode == 'edit')
		<script>Grocy.EditObjectId = {{ $location->id }};</script>
	@endif

	<form id="location-form">

		<div class="form-group">
			<label for="name">{{ $L('Name') }}</label>
			<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $location->name }}@endif">
			<div class="help-block with-errors"></div>
		</div>

		<div class="form-group">
			<label for="description">{{ $L('Description') }}</label>
			<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $location->description }}@endif</textarea>
		</div>

		<button id="save-location-button" type="submit" class="btn btn-default">{{ $L('Save') }}</button>

	</form>

</div>
@stop
