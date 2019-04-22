@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit userfield'))
@else
	@section('title', $L('Create userfield'))
@endif

@section('viewJsName', 'userfieldform')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $userfield->id }};</script>
		@endif

		<form id="userfield-form" novalidate>

			<div class="form-group">
				<label for="entity">{{ $L('Entity') }}</label>
				<select required class="form-control" id="entity" name="entity">
					<option></option>
					@foreach($entities as $entity)
						<option @if($mode == 'edit' && $userfield->entity == $entity) selected="selected" @endif value="{{ $entity }}">{{ $entity }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A entity is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $userfield->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $L('Caption') }}</label>
				<input type="text" class="form-control" required id="caption" name="caption" value="@if($mode == 'edit'){{ $userfield->caption }}@endif">
				<div class="invalid-feedback">{{ $L('A caption is required') }}</div>
			</div>

			<div class="form-group">
				<label for="entity">{{ $L('Type') }}</label>
				<select required class="form-control" id="type" name="type">
					<option></option>
					@foreach($userfieldTypes as $userfieldType)
						<option @if($mode == 'edit' && $userfield->type == $userfieldType) selected="selected" @endif value="{{ $userfieldType }}">{{ $L($userfieldType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $L('A type is required') }}</div>
			</div>

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="show_as_column_in_tables" value="0">
					<input @if($mode == 'edit' && $userfield->show_as_column_in_tables == 1) checked @endif class="form-check-input" type="checkbox" id="show_as_column_in_tables" name="show_as_column_in_tables" value="1">
					<label class="form-check-label" for="show_as_column_in_tables">{{ $L('Show as column in tables') }}</label>
				</div>
			</div>

			<button id="save-userfield-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>
</div>
@stop
