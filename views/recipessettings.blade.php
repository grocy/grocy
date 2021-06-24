@extends($rootLayout)

@section('title', $__t('Recipes settings'))

@section('viewJsName', 'recipessettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<h4 class="mt-2">{{ $__t('Recipe card') }}</h4>
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
