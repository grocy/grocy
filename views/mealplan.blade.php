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
	
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};

	Grocy.MealPlanFirstDayOfWeek = '{{ GROCY_MEAL_PLAN_FIRST_DAY_OF_WEEK }}';
</script>

<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>
<hr>

<div class="row">
	<div class="col">
		<div id="calendar"></div>
	</div>
</div>

<div class="modal fade" id="add-recipe-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="add-recipe-modal-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="add-recipe-form" novalidate>

					@include('components.recipepicker', array(
						'recipes' => $recipes,
						'isRequired' => true,
						'nextInputSelector' => '#recipe_servings'
					))

					@include('components.numberpicker', array(
						'id' => 'recipe_servings',
						'label' => 'Servings',
						'min' => 1,
						'value' => '1',
						'invalidFeedback' => $__t('This cannot be lower than %s', '1')
					))

					<input type="hidden" id="day" name="day" value="">
					<input type="hidden" name="type" value="recipe">

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-add-recipe-button" data-dismiss="modal" class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="add-note-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="add-note-modal-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="add-note-form" novalidate>

					<div class="form-group">
						<label for="note">{{ $__t('Note') }}</label>
						<textarea class="form-control" rows="2" id="note" name="note"></textarea>
					</div>

					<input type="hidden" name="type" value="note">

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-add-note-button" data-dismiss="modal" class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="add-product-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="add-product-modal-title" class="modal-title w-100"></h4>
			</div>
			<div class="modal-body">
				<form id="add-product-form" novalidate>

					@include('components.productpicker', array(
						'products' => $products,
						'nextInputSelector' => '#amount'
					))

					@include('components.productamountpicker', array(
						'value' => 1,
						'additionalGroupCssClasses' => 'mb-0'
					))

					<input type="hidden" name="type" value="product">

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-add-product-button" data-dismiss="modal" class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
