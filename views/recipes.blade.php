@extends('layout.default')

@section('title', $__t('Recipes'))
@section('activeNav', 'recipes')
@section('viewJsName', 'recipes')

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="row">
	
	<div class="col-xs-12 col-md-6 pb-3">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/recipe/new') }}">
				<i class="fas fa-plus"></i> {{ $__t('Add') }}
			</a>
		</h1>

		<div class="row">
			<div class="col-6">
				<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
				<input type="text" class="form-control" id="search">
			</div>

			<div class="col-6">
				<label for="status-filter">{{ $__t('Filter by status') }}</label> <i class="fas fa-filter"></i>
				<select class="form-control" id="status-filter">
					<option class="bg-white" value="all">{{ $__t('All') }}</option>
					<option class="bg-success" value="enoughtinstock">{{ $__t('Enough in stock') }}</option>
					<option class="bg-warning" value="enoughinstockwithshoppinglist">{{ $__t('Not enough in stock, but already on the shopping list') }}</option>
					<option class="bg-danger" value="notenoughinstock">{{ $__t('Not enough in stock') }}</option>
				</select>
			</div>
		</div>

		<ul class="nav nav-tabs mt-3">
			<li class="nav-item">
				<a class="nav-link active" id="list-tab" data-toggle="tab" href="#list">{{ $__t('List') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery">{{ $__t('Gallery') }}</a>
			</li>
		</ul>

		<div class="tab-content">

			<div class="tab-pane show active" id="list">
				<table id="recipes-table" class="table table-striped dt-responsive">
					<thead>
						<tr>
							<th>{{ $__t('Name') }}</th>
							<th>{{ $__t('Desired servings') }}</th>
							<th>{{ $__t('Requirements fulfilled') }}</th>
							<th class="d-none">Hidden status for sorting of "Requirements fulfilled" column</th>
							<th class="d-none">Hidden status for filtering by status</th>
							<th class="d-none">Hidden recipe ingredient product names</th>

							@include('components.userfields_thead', array(
								'userfields' => $userfields
							))

						</tr>
					</thead>
					<tbody class="d-none">
						@foreach($recipes as $recipe)
						<tr id="recipe-row-{{ $recipe->id }}" data-recipe-id="{{ $recipe->id }}">
							<td>
								{{ $recipe->name }}
							</td>
							<td>
								{{ $recipe->desired_servings }}
							</td>
							<td>
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
								<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
							</td>
							<td class="d-none">
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count }}
							</td>
							<td class="d-none">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) enoughtinstock @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) enoughinstockwithshoppinglist @else notenoughinstock @endif
							</td>
							<td class="d-none">
								@foreach(FindAllObjectsInArrayByPropertyValue($recipePositionsResolved, 'recipe_id', $recipe->id) as $recipePos)
									{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name . ' ' }}
								@endforeach
							</td>

							@include('components.userfields_tbody', array(
								'userfields' => $userfields,
								'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $recipe->id)
							))

						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="tab-pane show" id="gallery">
				<div class="row no-gutters">
					@foreach($recipes as $recipe)
					<div class="col-6 recipe-gallery-item-container">
						<a class="discrete-link recipe-gallery-item" data-recipe-id="{{ $recipe->id }}" href="#">
							<div id="recipe-card-{{ $recipe->id }}" class="card border-white mb-0 recipe-card">
								@if(!empty($recipe->picture_file_name))
								<img data-src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}" class="img-fluid lazy">
								@endif
								<div class="card-body text-center">
									<h5 class="card-title mb-1">{{ $recipe->name }}</h5>
									<p class="card-text">
										@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
										<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
									</p>
								</div>
							</div>
						</a>
					</div>
				@endforeach
				</div>
			</div>
			
		</div>
	</div>

	@if($selectedRecipe !== null)
	<div class="col-xs-12 col-md-6">
		<div id="selectedRecipeCard" class="card">
			<div class="card-header card-header-fullscreen">
				<i class="fas fa-cocktail"></i> {{ $selectedRecipe->name }}&nbsp;&nbsp;
				<a id="selectedRecipeConsumeButton" class="btn btn-sm btn-outline-success py-0 hide-when-embedded hide-on-fullscreen-card @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled == 0) disabled @endif" href="#" data-toggle="tooltip" title="{{ $__t('Consume all ingredients needed by this recipe') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-utensils"></i>
				</a>
				<a class="btn btn-sm btn-outline-primary py-0 recipe-order-missing-button hide-when-embedded hide-on-fullscreen-card @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled_with_shopping_list == 1) disabled @endif" href="#" data-toggle="tooltip" title="{{ $__t('Put missing products on shopping list') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-cart-plus"></i>
				</a>&nbsp;&nbsp;
				<a id="selectedRecipeEditButton" class="btn btn-sm btn-outline-info hide-when-embedded hide-on-fullscreen-card py-0" href="{{ $U('/recipe/') }}{{ $selectedRecipe->id }}">
					<i class="fas fa-edit"></i>
				</a>
				<a id="selectedRecipeDeleteButton" class="btn btn-sm btn-outline-danger hide-when-embedded hide-on-fullscreen-card py-0" href="#" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-trash"></i>
				</a>
				<a id="selectedRecipeToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 hide-when-embedded float-right" href="#" data-toggle="tooltip" title="{{ $__t('Expand to fullscreen') }}">
					<i class="fas fa-expand-arrows-alt"></i>
				</a>
			</div>

			<div class="card-body mb-0 pb-0">

				<div class="row">
					<div class="col-4">
						@include('components.numberpicker', array(
							'id' => 'servings-scale',
							'label' => 'Desired servings',
							'min' => 1,
							'value' => $selectedRecipe->desired_servings,
							'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
							'additionalAttributes' => 'data-recipe-id="' . $selectedRecipe->id . '"',
							'hint' => $__t('Base: %s', $selectedRecipe->base_servings)
						))
					</div>
					@if(!empty($selectedRecipeTotalCalories) && intval($selectedRecipeTotalCalories) > 0)
					<div class="col-2">
						<label>{{ $__t('Energy (kcal)') }}</label>
						<p class="mb-0">
							<h3 class="locale-number locale-number-generic pt-0">{{ $selectedRecipeTotalCalories }}</h3>
						</p>
					</div>
					@endif
					@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<div class="col-6">
						<label>{{ $__t('Costs') }}&nbsp;&nbsp;
							<span class="small text-muted">{{ $__t('Based on the prices of the last purchase per product') }}</span>
						</label>
						<p class="mb-0">
							<h3 class="locale-number locale-number-currency pt-0">{{ $selectedRecipeTotalCosts }}</h3>
						</p>
					</div>
					@endif
				</div>

				<!-- Subrecipes first -->
				@foreach($selectedRecipeSubRecipes as $selectedRecipeSubRecipe)
					<h3 class="mb-2">{{ $selectedRecipeSubRecipe->name }}</h3>

					@if(!empty($selectedRecipeSubRecipe->picture_file_name))
					<p class="w-75 mx-auto text-center"><img src="{{ $U('/api/files/recipepictures/' . base64_encode($selectedRecipeSubRecipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}" class="img-fluid img-thumbnail lazy"></p>
					@endif

					@php $selectedRecipeSubRecipePositionsFiltered = FindAllObjectsInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'child_recipe_id', $selectedRecipeSubRecipe->id); @endphp
					@if(count($selectedRecipeSubRecipePositionsFiltered) > 0)
					<h5 class="mb-0">{{ $__t('Ingredients') }}</h5>
					<ul class="list-group list-group-flush">
						@php
							$lastIngredientGroup = 'undefined';
							$lastProductGroup = 'undefined';
							$hasIngredientGroups = false;
							$hasProductGroups = false;
						@endphp
						@foreach($selectedRecipeSubRecipePositionsFiltered as $selectedRecipePosition)
						@if($lastIngredientGroup != $selectedRecipePosition->ingredient_group && !empty($selectedRecipePosition->ingredient_group))
							@php $hasIngredientGroups = true; @endphp
							<h5 class="mb-2 mt-2 ml-1"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
						@endif
						@if(boolval($userSettings['recipe_ingredients_group_by_product_group']) && $lastProductGroup != $selectedRecipePosition->product_group && !empty($selectedRecipePosition->product_group))
							@php $hasProductGroups = true; @endphp
							<h6 class="mb-2 mt-2 @if($hasIngredientGroups) ml-3 @else ml-1 @endif"><strong>{{ $selectedRecipePosition->product_group }}</strong></h6>
						@endif
						<li class="list-group-item px-0 @if($hasIngredientGroups && $hasProductGroups) ml-4 @elseif($hasIngredientGroups || $hasProductGroups) ml-2 @else ml-0 @endif">
							@php
								$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
								$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
								$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
								$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
								if ($productQuConversion)
								{
									$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
								}
							@endphp
							@if(!empty($selectedRecipePosition->recipe_variable_amount))
								{{ $selectedRecipePosition->recipe_variable_amount }}
							@else
								<span class="locale-number locale-number-quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
							@endif
							{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
							@if($selectedRecipePosition->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
							<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2)) }} @endif</span>

							@if(!empty($selectedRecipePosition->recipe_variable_amount))
								<div class="small text-muted font-italic">{{ $__t('Variable amount') }}</div>
							@endif

							@if(!empty($selectedRecipePosition->note))
							<div class="text-muted">{!! nl2br($selectedRecipePosition->note) !!}</div>
							@endif
						</li>
						@php $lastProductGroup = $selectedRecipePosition->product_group; @endphp
						@php $lastIngredientGroup = $selectedRecipePosition->ingredient_group; @endphp
						@endforeach
					</ul>
					@endif

					@if(!empty($selectedRecipeSubRecipe->description))
					<h5 class="mt-4">{{ $__t('Preparation') }}</h5>
					{!! $selectedRecipeSubRecipe->description !!}
					@endif
				@endforeach

				<!-- Selected recipe -->
				@if(!empty($selectedRecipe->picture_file_name))
				<p class="w-75 mx-auto text-center"><img src="{{ $U('/api/files/recipepictures/' . base64_encode($selectedRecipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}" class="img-fluid img-thumbnail lazy"></p>
				@endif

				@if($selectedRecipePositionsResolved->count() > 0)
				<h5 class="mb-0">{{ $__t('Ingredients') }}</h5>
				<ul class="list-group list-group-flush">
					@php
						$lastIngredientGroup = 'undefined';
						$lastProductGroup = 'undefined';
						$hasIngredientGroups = false;
						$hasProductGroups = false;
					@endphp
					@foreach($selectedRecipePositionsResolved as $selectedRecipePosition)
					@if($lastIngredientGroup != $selectedRecipePosition->ingredient_group && !empty($selectedRecipePosition->ingredient_group))
						@php $hasIngredientGroups = true; @endphp
						<h5 class="mb-2 mt-2 ml-1"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
					@endif
					@if(boolval($userSettings['recipe_ingredients_group_by_product_group']) && $lastProductGroup != $selectedRecipePosition->product_group && !empty($selectedRecipePosition->product_group))
						@php $hasProductGroups = true; @endphp
						<h6 class="mb-2 mt-2 @if($hasIngredientGroups) ml-3 @else ml-1 @endif"><strong>{{ $selectedRecipePosition->product_group }}</strong></h6>
					@endif
					<li class="list-group-item px-0 @if($hasIngredientGroups && $hasProductGroups) ml-4 @elseif($hasIngredientGroups || $hasProductGroups) ml-2 @else ml-0 @endif">
						@php
							$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
							$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
							$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
							$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
							if ($productQuConversion)
							{
								$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
							}
						@endphp
						@if(!empty($selectedRecipePosition->recipe_variable_amount))
							{{ $selectedRecipePosition->recipe_variable_amount }}
						@else
							<span class="locale-number locale-number-quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
						@endif
						{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
						@if($selectedRecipePosition->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2)) }} @endif</span>

						@if(!empty($selectedRecipePosition->recipe_variable_amount))
							<div class="small text-muted font-italic">{{ $__t('Variable amount') }}</div>
						@endif

						@if(!empty($selectedRecipePosition->note))
						<div class="text-muted">{!! nl2br($selectedRecipePosition->note) !!}</div>
						@endif
					</li>
					@php $lastProductGroup = $selectedRecipePosition->product_group; @endphp
					@php $lastIngredientGroup = $selectedRecipePosition->ingredient_group; @endphp
					@endforeach
				</ul>
				@endif

				@if(!empty($selectedRecipe->description))
				<h5 class="mt-4">{{ $__t('Preparation') }}</h5>
				{!! $selectedRecipe->description !!}
				@endif
			</div>
		</div>
	</div>
	@endif
</div>

<div id="missing-recipe-pos-list" class="list-group d-none mt-3">
	@foreach($recipePositionsResolved as $recipePos)
		@if(in_array($recipePos->recipe_id, $includedRecipeIdsAbsolute) && $recipePos->missing_amount > 0)
			<a href="#" class="list-group-item list-group-item-action list-group-item-primary missing-recipe-pos-select-button">
				<div class="form-check form-check-inline">
					<input class="form-check-input missing-recipe-pos-product-checkbox" type="checkbox" data-product-id="{{ $recipePos->product_id }}" checked>
				</div>
				{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name }}
			</a>
		@endif
	@endforeach
</div>
@stop
