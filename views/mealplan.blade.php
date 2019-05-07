@extends('layout.default')

@section('title', $__t('Meal plan'))
@section('activeNav', 'mealplan')
@section('viewJsName', 'mealplan')

@push('pageScripts')
	<script src="{{ $U('/node_modules/fullcalendar/dist/fullcalendar.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('fullcalendar_locale') && $__t('fullcalendar_locale') != 'x'))<script src="{{ $U('/node_modules', true) }}/fullcalendar/dist/locale/{{ $__t('fullcalendar_locale') }}.js?v={{ $version }}"></script>@endif
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/fullcalendar/dist/fullcalendar.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<script>
	var fullcalendarEventSources = {!! json_encode(array($fullcalendarEventSources)) !!}
	var internalRecipes = {!! json_encode($internalRecipes) !!}
	var recipesResolved = {!! json_encode($recipesResolved) !!}
</script>

<div class="row">
	<div class="col">
		<h1>
			@yield('title')
		</h1>
	</div>
</div>

<div class="row">
	<div class="col">
		<div id="calendar"></div>
	</div>
</div>

<div class="modal fade" id="add-recipe-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-header">
				<h4 id="add-recipe-modal-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="add-recipe-form" novalidate>

					@include('components.recipepicker', array(
						'recipes' => $recipes,
						'isRequired' => true,
						'nextInputSelector' => '#servings'
					))

					@include('components.numberpicker', array(
						'id' => 'servings',
						'label' => 'Servings',
						'min' => 1,
						'value' => '1',
						'invalidFeedback' => $__t('This cannot be lower than %s', '1')
					))

					<input type="hidden" id="day" name="day" value="">

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-add-recipe-button" data-dismiss="modal" class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
