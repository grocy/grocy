@extends('layout.default')

@if($mode == 'edit')
	@section('title', $__t('Edit equipment'))
@else
	@section('title', $__t('Create equipment'))
@endif

@section('viewJsName', 'equipmentform')

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
			<script>Grocy.EditObjectId = {{ $equipment->id }};</script>

			@if(!empty($equipment->instruction_manual_file_name))
				<script>Grocy.InstructionManualFileNameName = '{{ $equipment->instruction_manual_file_name }}';</script>
			@endif
		@endif

		<form id="equipment-form" novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text" class="form-control" required id="name" name="name" value="@if($mode == 'edit'){{ $equipment->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="instruction-manual">{{ $__t('Instruction manual') }} (PDF)
					<span class="text-muted small">{{ $__t('If you don\'t select a file, the current instruction manual will not be altered') }}</span>
				</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="instruction-manual" accept="application/pdf">
					<label class="custom-file-label" for="instruction-manual">{{ $__t('No file selected') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Notes') }}</label>
				<textarea class="form-control wysiwyg-editor" id="description" name="description">@if($mode == 'edit'){{ $equipment->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
				'userfields' => $userfields,
				'entity' => 'equipment'
			))

			<button id="save-equipment-button" class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-xs-12">
		<label class="mt-2">{{ $__t('Instruction manual') }}</label>
		<button id="delete-current-instruction-manual-button" class="btn btn-sm btn-danger mb-1 @if(empty($equipment->instruction_manual_file_name)) disabled @endif"><i class="fas fa-trash"></i> {{ $__t('Delete') }}</button>
		@if(!empty($equipment->instruction_manual_file_name))
			<embed id="current-equipment-instruction-manual" class="embed-responsive embed-responsive-4by3" src="{{ $U('/api/files/equipmentmanuals/' . base64_encode($equipment->instruction_manual_file_name)) }}" type="application/pdf">
			<p id="delete-current-instruction-manual-on-save-hint" class="form-text text-muted font-italic d-none">{{ $__t('The current instruction manual will be deleted when you save the equipment') }}</p>
		@else
			<p id="no-current-instruction-manual-hint" class="form-text text-muted font-italic">{{ $__t('No instruction manual available') }}</p>
		@endif
	</div>
</div>
@stop
