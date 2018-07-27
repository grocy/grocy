@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit quantity unit'))
@else
	@section('title', $L('Create quantity unit'))
@endif

@section('viewJsName', 'quantityunitform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $quantityunit->id }};</script>
		@endif

		<form id="quantityunit-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }} <span class="small text-muted">{{ $L('in singular form') }}</span></label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $quantityunit->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name_plural">{{ $L('Name') }} <span class="small text-muted">{{ $L('in plural form') }}</span></label>
				<input type="text" class="form-control" id="name_plural" name="name_plural" value="@if($mode == 'edit'){{ $quantityunit->name_plural }}@endif">
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="2" id="description" name="description">@if($mode == 'edit'){{ $quantityunit->description }}@endif</textarea>
			</div>

			<button id="save-quantityunit-button" type="submit" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
