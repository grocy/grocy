@php require_frontend_packages(['summernote']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit equipment'))
@else
@section('title', $__t('Create equipment'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $equipment->id }};
		</script>

		@if(!empty($equipment->instruction_manual_file_name))
		<script>
			Grocy.InstructionManualFileNameName = '{{ $equipment->instruction_manual_file_name }}';
		</script>
		@endif
		@endif

		<form id="equipment-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $equipment->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Notes') }}</label>
				<textarea class="form-control wysiwyg-editor"
					id="description"
					name="description">@if($mode == 'edit'){{ $equipment->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'equipment'
			))

			<button id="save-equipment-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-12">
		<div class="row">
			<div class="col">
				<div class="title-related-links mb-3">
					<h4>
						{{ $__t('Instruction manual') }}
					</h4>
					<div class="form-group w-75 m-0">
						<div class="input-group">
							<div class="custom-file">
								<input type="file"
									class="custom-file-input"
									id="instruction-manual"
									accept="application/pdf">
								<label id="instruction-manual-label"
									class="custom-file-label @if(empty($equipment->instruction_manual_file_name)) d-none @endif"
									for="instruction-manual">
									{{ $equipment->instruction_manual_file_name }}
								</label>
								<label id="instruction-manual-label-none"
									class="custom-file-label @if(!empty($equipment->instruction_manual_file_name)) d-none @endif"
									for="instruction-manual">
									{{ $__t('No file selected') }}
								</label>
							</div>
							<div class="input-group-append">
								<span class="input-group-text"><i class="fa-solid fa-trash"
										id="delete-current-instruction-manual-button"></i></span>
							</div>
						</div>
					</div>
				</div>
				@if(!empty($equipment->instruction_manual_file_name))
				<embed id="current-equipment-instruction-manual"
					class="embed-responsive embed-responsive-4by3"
					src="{{ $U('/api/files/equipmentmanuals/' . base64_encode($equipment->instruction_manual_file_name)) }}"
					type="application/pdf">
				<p id="delete-current-instruction-manual-on-save-hint"
					class="form-text text-muted font-italic d-none">{{ $__t('The current file will be deleted on save') }}</p>
				@endif
			</div>
		</div>
	</div>
</div>
@stop
