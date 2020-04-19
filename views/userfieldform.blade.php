@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit userfield'))
@else
	@section('title', $__t('Create userfield'))
@endif

@section('viewJsName', 'userfieldform')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $userfield->id }};</script>
		@endif

		<form id="userfield-form" novalidate>

			<div class="form-group">
				<label for="entity">{{ $__t('Entity') }}</label>
				<select required class="form-control" id="entity" name="entity">
					<option></option>
					@foreach($entities as $entity)
						<option @if($mode == 'edit' && $userfield->entity == $entity) selected="selected" @endif value="{{ $entity }}">{{ $entity }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A entity is required') }}</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required pattern="^[a-zA-Z0-9]*$" id="name" name="name" value="@if($mode == 'edit'){{ $userfield->name }}@endif">
				<div class="invalid-feedback">{{ $__t('This is required and can only contain letters and numbers') }}</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Caption') }}</label>
				<input type="text" class="form-control" required id="caption" name="caption" value="@if($mode == 'edit'){{ $userfield->caption }}@endif">
				<div class="invalid-feedback">{{ $__t('A caption is required') }}</div>
			</div>

			<div class="form-group">
				<label for="type">{{ $__t('Type') }}</label>
				<select required class="form-control" id="type" name="type">
					<option></option>
					@foreach($userfieldTypes as $userfieldType)
						<option @if($mode == 'edit' && $userfield->type == $userfieldType) selected="selected" @endif value="{{ $userfieldType }}">{{ $__t($userfieldType) }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A type is required') }}</div>
			</div>

			<div class="form-group d-none">
				<label for="config">{{ $__t('Configuration') }} <span id="config-hint" class="small text-muted"></span></label>
				<textarea class="form-control" rows="10" id="config" name="config">@if($mode == 'edit'){{ $userfield->config }}@endif</textarea>
			</div>

			<div class="form-group">
				<div class="form-check">
					<input type="hidden" name="show_as_column_in_tables" value="0">
					<input @if($mode == 'edit' && $userfield->show_as_column_in_tables == 1) checked @endif class="form-check-input" type="checkbox" id="show_as_column_in_tables" name="show_as_column_in_tables" value="1">
					<label class="form-check-label" for="show_as_column_in_tables">{{ $__t('Show as column in tables') }}</label>
				</div>
			</div>

			<button id="save-userfield-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
