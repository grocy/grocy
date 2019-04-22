@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit location'))
@else
	@section('title', $L('Create location'))
@endif

@section('viewJsName', 'locationform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $location->id }};</script>
		@endif

		<form id="location-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $location->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $location->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'locations'
			))

			<button id="save-location-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
