@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit task category'))
@else
@section('title', $__t('Create task category'))
@endif

@section('viewJsName', 'taskcategoryform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
@if($mode == 'edit')	
EditObjectId: {{ $category->id }},
@endif
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">
@php
$classes = $embedded ? '' : 'col-lg-6';
@endphp

<div class="row">
	<div class="{{ $classes }} col-12">
		<form id="task-category-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $category->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control"
					rows="2"
					id="description"
					name="description">@if($mode == 'edit'){{ $category->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'task_categories'
			))

			<button id="save-task-category-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
