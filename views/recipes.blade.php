@extends('layout.default')

@section('title', $L('Recipes'))
@section('activeNav', 'recipes')
@section('viewJsName', 'recipes')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/recipe/new') }}">
				<i class="fas fa-plus"></i> {{ $L('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-md-6 pb-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">

		<table id="recipes-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th>{{ $L('Name') }}</th>
					<th>{{ $L('Requirements fulfilled') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($recipes as $recipe)
				<tr data-recipe-id="{{ $recipe->id }}">
					<td class="fit-content">
						<a class="btn btn-sm btn-info" href="{{ $U('/recipe/') }}{{ $recipe->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger recipe-delete-button" href="#" data-recipe-id="{{ $recipe->id }}" data-recipe-name="{{ $recipe->name }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary recipe-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesSumFulfillment, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ disabled }}@endif" href="#" title="{{ $L('Put missing products on shopping list') }}" data-recipe-id="{{ $recipe->id }}" data-recipe-name="{{ $recipe->name }}">
							<i class="fas fa-cart-plus"></i>
						</a>
					</td>
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
				<i class="fas fa-cocktail"></i> {{ $selectedRecipe->name }}
				<a id="selectedRecipeToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" title="{{ $L('Expand to fullscreen') }}">
					<i class="fas fa-expand-arrows-alt"></i>
				</a>
			</div>
			<div class="card-body">
				<h5 class="mb-0">{{ $L('Ingredients') }}</h5>
			</div>
			<ul class="list-group list-group-flush">
				@foreach($selectedRecipePositions as $selectedRecipePosition)
				<li class="list-group-item">
					{{ $selectedRecipePosition->amount }} {{ Pluralize($selectedRecipePosition->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->qu_id_stock)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
					<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $L('Enough in stock') }} @else {{ $L('Not enough in stock, #1 missing, #2 already on shopping list', FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, FindObjectInArrayByPropertyValue($recipesFulfillment, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list) }} @endif</span>

					@if(!empty($selectedRecipePosition->note))
					<div class="text-muted">{{ $selectedRecipePosition->note }} </div>
					@endif
				</li>
				@endforeach
			</ul>
			<div class="card-body">
				<h5>{{ $L('Preparation') }}</h5>
				{!! nl2br(htmlentities($selectedRecipe->description)) !!}
			</div>
		</div>
	</div>
	@endif
</div>
@stop
