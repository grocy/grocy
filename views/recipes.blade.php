@extends('layout.default')

@section('title', $L('Recipes'))
@section('activeNav', 'recipes')
@section('viewJsName', 'recipes')

@section('content')
<div class="row">
	
	<div class="col-xs-12 col-md-6 pb-3">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/recipe/new') }}">
				<i class="fas fa-plus"></i> {{ $L('Add') }}
			</a>
		</h1>

		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">

		<table id="recipes-table" class="table table-striped dt-responsive">
			<thead>
				<tr>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Requirements fulfilled') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($recipes as $recipe)
				<tr data-recipe-id="{{ $recipe->id }}">
					<td>
						{{ $recipe->name }}
					</td>
					<td>
						@if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $L('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $L('Not enough in stock, #1 ingredients missing but already on the shopping list', FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->missing_products_count) }}@else{{ $L('Not enough in stock, #1 ingredients missing', FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->missing_products_count) }}@endif</span>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if($selectedRecipe !== null)
	<div class="col-xs-12 col-md-6">
		<div id="selectedRecipeCard" class="card">
			<div class="card-header">
				<i class="fas fa-cocktail"></i> {{ $selectedRecipe->name }}&nbsp;&nbsp;
				<a id="selectedRecipeConsumeButton" class="btn btn-sm btn-outline-success py-0" href="#" data-toggle="tooltip" title="{{ $L('Consume all ingredients needed by this recipe') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-utensils"></i>
				</a>
				<a class="btn btn-sm btn-outline-primary py-0 recipe-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $selectedRecipe->id)->need_fulfilled_with_shopping_list == 1){{ disabled }}@endif" href="#" data-toggle="tooltip" title="{{ $L('Put missing products on shopping list') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-cart-plus"></i>
				</a>&nbsp;&nbsp;
				<a id="selectedRecipeEditButton" class="btn btn-sm btn-outline-info py-0" href="{{ $U('/recipe/') }}{{ $selectedRecipe->id }}">
					<i class="fas fa-edit"></i>
				</a>
				<a id="selectedRecipeDeleteButton" class="btn btn-sm btn-outline-danger py-0" href="#" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-trash"></i>
				</a>
				<a id="selectedRecipeToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $L('Expand to fullscreen') }}">
					<i class="fas fa-expand-arrows-alt"></i>
				</a>
			</div>
			<div class="card-body">
				<h5 class="mb-0">{{ $L('Ingredients') }}</h5>
			</div>
			<ul class="list-group list-group-flush">
				@foreach($selectedRecipePositions as $selectedRecipePosition)
				<li class="list-group-item">
					{{ $selectedRecipePosition->amount }} {{ Pluralize($selectedRecipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
					<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock, #1 missing, #2 already on shopping list', FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list) }} @endif</span>

					@if(!empty($selectedRecipePosition->note))
					<div class="text-muted">{{ $selectedRecipePosition->note }} </div>
					@endif
				</li>
				@endforeach
			</ul>
			<div class="card-body">
				<h5>{{ $L('Preparation') }}</h5>
				{!! $selectedRecipe->description !!}
			</div>
		</div>
	</div>
	@endif
</div>
@stop
