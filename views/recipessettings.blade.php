@extends('layout.default')

@section('title', $__t('Recipes settings'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-4 col-md-8 col-12">
		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="recipes_show_list_side_by_side"
					data-setting-key="recipes_show_list_side_by_side">
				<label class="form-check-label custom-control-label"
					for="recipes_show_list_side_by_side">
					{{ $__t('Show the recipe list and the recipe side by side') }}
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="recipes_show_ingredient_checkbox"
					data-setting-key="recipes_show_ingredient_checkbox">
				<label class="form-check-label custom-control-label"
					for="recipes_show_ingredient_checkbox">
					{{ $__t('Show a little checkbox next to each ingredient to mark it as done') }}
					<i class="fa-solid fa-question-circle text-muted"
						data-toggle="tooltip"
						data-trigger="hover click"
						title="{{ $__t('The ingredient is crossed out when clicked, the status is not saved, means reset when the page is reloaded') }}"></i>
				</label>
			</div>
		</div>

		<h4 class="mt-5">{{ $__t('Recipe card') }}</h4>
		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="recipe_ingredients_group_by_product_group"
					data-setting-key="recipe_ingredients_group_by_product_group">
				<label class="form-check-label custom-control-label"
					for="recipe_ingredients_group_by_product_group">
					{{ $__t('Group ingredients by their product group') }}
				</label>
			</div>
		</div>

		<a href="{{ $U('/recipes') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
