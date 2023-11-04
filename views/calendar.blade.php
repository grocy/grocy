@php require_frontend_packages(['fullcalendar', 'bwipjs']); @endphp

@extends('layout.default')

@section('title', $__t('Calendar'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<button class="btn btn-outline-dark d-md-none mt-2 float-right order-1 order-md-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100 d-print-none"
				id="related-links">
				<a id="ical-button"
					class="btn btn-outline-dark m-1 mt-md-0 mb-md-0 float-right"
					href="#">
					{{ $__t('Share/Integrate calendar (iCal)') }}
				</a>
				<a id="configure-colors-button"
					class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
					href="#">
					{{ $__t('Configure colors') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<script>
	var fullcalendarEventSources = {!! json_encode(array($fullcalendarEventSources)) !!}
</script>

<div class="row">
	<div class="col">
		<div id="calendar"></div>
	</div>
</div>

<div class="modal fade"
	id="configure-colors-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title w-100">{{ $__t('Configure colors') }}</h4>
			</div>
			<div class="modal-body">
				@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">{{ $__t('Products') }}</span>
						</div>
						<input id="calendar_color_products"
							data-setting-key="calendar_color_products"
							class="form-control user-setting-control"
							type="color"
							value={{$userSettings['calendar_color_products']}}>
					</div>
				</div>
				@endif

				@if(GROCY_FEATURE_FLAG_TASKS)
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">{{ $__t('Tasks') }}</span>
						</div>
						<input id="calendar_color_tasks"
							data-setting-key="calendar_color_tasks"
							class="form-control user-setting-control"
							type="color"
							value={{$userSettings['calendar_color_tasks']}}>
					</div>
				</div>
				@endif

				@if(GROCY_FEATURE_FLAG_CHORES)
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">{{ $__t('Chores') }}</span>
						</div>
						<input id="calendar_color_chores"
							data-setting-key="calendar_color_chores"
							class="form-control user-setting-control"
							type="color"
							value={{$userSettings['calendar_color_chores']}}>
					</div>
				</div>
				@endif

				@if(GROCY_FEATURE_FLAG_BATTERIES)
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">{{ $__t('Batteries') }}</span>
						</div>
						<input id="calendar_color_batteries"
							data-setting-key="calendar_color_batteries"
							class="form-control user-setting-control"
							type="color"
							value={{$userSettings['calendar_color_batteries']}}>
					</div>
				</div>
				@endif

				@if(GROCY_FEATURE_FLAG_RECIPES_MEALPLAN)
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">{{ $__t('Meal plan') }}</span>
						</div>
						<input id="calendar_color_meal_plan"
							data-setting-key="calendar_color_meal_plan"
							class="form-control user-setting-control"
							type="color"
							value={{$userSettings['calendar_color_meal_plan']}}>
					</div>
				</div>
				@endif
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-success"
					data-dismiss="modal">{{ $__t('OK') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
