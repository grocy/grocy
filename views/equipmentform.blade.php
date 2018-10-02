@extends('layout.default')

@if($mode == 'edit')
	@section('title', $L('Edit equipment'))
@else
	@section('title', $L('Create equipment'))
@endif

@section('viewJsName', 'equipmentform')

@section('content')
<div class="row">

	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<script>Grocy.EditMode = '{{ $mode }}';</script>

		@if($mode == 'edit')
			<script>Grocy.EditObjectId = {{ $equipment->id }};</script>

			@if(!empty($equipment->instruction_manual_file_name))
				<script>Grocy.InstructionManualFileNameName = '{{ $equipment->instruction_manual_file_name }}';</script>
			@endif
		@endif

		<form id="equipment-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $L('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $equipment->name }}@endif">
				<div class="invalid-feedback">{{ $L('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="instruction-manual">{{ $L('Instruction manual') }} (PDF)</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="instruction-manual" accept="application/pdf">
					<label class="custom-file-label" for="instruction-manual">{{ $L('No file selected') }}</label>
				</div>
				<p class="form-text text-muted small">{{ $L('If you don\'t select a file, the current instruction manual will not be altered') }}</p>
			</div>

			<div class="form-group">
				<label for="description">{{ $L('Description') }}</label>
				<textarea class="form-control" rows="25" id="description" name="description">@if($mode == 'edit'){{ $equipment->description }}@endif</textarea>
			</div>

			<button id="save-equipment-button" class="btn btn-success">{{ $L('Save') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		<label class="mt-2">{{ $L('Current instruction manual') }}</label>
		<button id="delete-current-instruction-manual-button" class="btn btn-sm btn-danger @if(empty($equipment->instruction_manual_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $L('Delete') }}</button>
		@if(!empty($equipment->instruction_manual_file_name))
			<p>TODO: Here the current instruction manual needs to be shown (PDF.js), if any...</p>
			<p id="delete-current-instruction-manual-on-save-hint" class="form-text text-muted font-italic d-none">{{ $L('The current instruction manual will be deleted when you save the equipment') }}</p>
		@else
			<p id="no-current-instruction-manual-hint" class="form-text text-muted font-italic">{{ $L('No instruction manual') }}</p>
		@endif
	</div>
</div>
@stop
