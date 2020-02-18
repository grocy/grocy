@extends('layout.default')

@section('title', $__t('Recipes settings'))

@section('viewJsName', 'recipessettings')

@section('content')
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h1>@yield('title')</h1>

		<h4 class="mt-2">{{ $__t('Recipe card') }}</h4>
		<div class="form-group">
			<div class="checkbox">
				<label for="recipe_ingredients_group_by_product_group">
					<input type="checkbox" class="user-setting-control" id="recipe_ingredients_group_by_product_group" data-setting-key="recipe_ingredients_group_by_product_group"> {{ $__t('Group ingredients by their product group') }}
				</label>
			</div>
		</div>

		<a href="{{ $U('/recipes') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
