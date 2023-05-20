@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit meal plan section'))
@else
@section('title', $__t('Create meal plan section'))
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
			Grocy.EditObjectId = {{ $mealplanSection->id }};
		</script>
		@endif

		<form id="mealplansection-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $mealplanSection->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			@php if($mode == 'edit' && !empty($mealplanSection->sort_number)) { $value = $mealplanSection->sort_number; } else { $value = ''; } @endphp
			@include('components.numberpicker', array(
			'id' => 'sort_number',
			'label' => 'Sort number',
			'min' => 0,
			'value' => $value,
			'isRequired' => false,
			'hint' => $__t('Sections will be ordered by that number on the meal plan')
			))

			<div class="form-group">
				<label for="time_info">{{ $__t('Time') }}</label>
				<input type="time"
					class="form-control"
					id="time_info"
					name="time_info"
					value="@if($mode == 'edit'){{ $mealplanSection->time_info }}@endif">
			</div>

			<button id="save-mealplansection-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
