@extends('layout.default')

@section('title', $__t('Recipes'))
@section('activeNav', 'recipes')
@section('viewJsName', 'recipes')

@section('content')
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
							<th>{{ $__t('Servings') }}</th>
							<th>{{ $__t('Requirements fulfilled') }}</th>
							<th class="d-none">Hidden status for sorting of "Requirements fulfilled" column</th>
							<th class="d-none">Hidden status for filtering by status</th>

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
								<img src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name)) }}" class="img-fluid">
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
			<div class="card-header">
				<i class="fas fa-cocktail"></i> {{ $selectedRecipe->name }}&nbsp;&nbsp;
				<a id="selectedRecipeConsumeButton" class="btn btn-sm btn-outline-success py-0 @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled == 0) disabled @endif" href="#" data-toggle="tooltip" title="{{ $__t('Consume all ingredients needed by this recipe') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-utensils"></i>
				</a>
				<a class="btn btn-sm btn-outline-primary py-0 recipe-order-missing-button @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->need_fulfilled_with_shopping_list == 1) disabled @endif" href="#" data-toggle="tooltip" title="{{ $__t('Put missing products on shopping list') }}" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-cart-plus"></i>
				</a>&nbsp;&nbsp;
				<a id="selectedRecipeEditButton" class="btn btn-sm btn-outline-info py-0" href="{{ $U('/recipe/') }}{{ $selectedRecipe->id }}">
					<i class="fas fa-edit"></i>
				</a>
				<a id="selectedRecipeDeleteButton" class="btn btn-sm btn-outline-danger py-0" href="#" data-recipe-id="{{ $selectedRecipe->id }}" data-recipe-name="{{ $selectedRecipe->name }}">
					<i class="fas fa-trash"></i>
				</a>
				<a id="selectedRecipeToggleFullscreenButton" class="btn btn-sm btn-outline-secondary py-0 float-right" href="#" data-toggle="tooltip" title="{{ $__t('Expand to fullscreen') }}">
					<i class="fas fa-expand-arrows-alt"></i>
				</a>
			</div>

			<div class="card-body mb-0 pb-0">
				<div class="row">
					<div class="col-5">
						@include('components.numberpicker', array(
							'id' => 'servings-scale',
							'label' => 'Servings',
							'min' => 1,
							'value' => $selectedRecipe->desired_servings,
							'invalidFeedback' => $__t('This cannot be lower than %s', '1'),
							'additionalAttributes' => 'data-recipe-id="' . $selectedRecipe->id . '"'
						))
					</div>
					<div class="col-7">
						<label>{{ $__t('Costs') }}&nbsp;&nbsp;
							<span class="small text-muted">{{ $__t('Based on the prices of the last purchase per product') }}</span>
						</label>
						<p class="font-weight-bold font-italic">
							<span class="locale-number-format" data-format="currency">{{ $selectedRecipeTotalCosts }}</span>
						</p>
					</div>
				</div>
			</div>

			<!-- Subrecipes first -->
			@foreach($selectedRecipeSubRecipes as $selectedRecipeSubRecipe)
				<div class="card-body">
					<h3 class="mb-0">{{ $selectedRecipeSubRecipe->name }}</h3>
				</div>

				@if(!empty($selectedRecipeSubRecipe->picture_file_name))
					<p class="w-75 mx-auto"><img src="{{ $U('/api/files/recipepictures/' . base64_encode($selectedRecipeSubRecipe->picture_file_name)) }}" class="img-fluid img-thumbnail"></p>
				@endif

				@php $selectedRecipeSubRecipePositionsFiltered = FindAllObjectsInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'child_recipe_id', $selectedRecipeSubRecipe->id); @endphp
				@if(count($selectedRecipeSubRecipePositionsFiltered) > 0)
				<div class="card-body">
					<h5 class="mb-0">{{ $__t('Ingredients') }}</h5>
				</div>
				<ul class="list-group list-group-flush">
					@php $lastGroup = 'undefined'; @endphp
					@foreach($selectedRecipeSubRecipePositionsFiltered as $selectedRecipePosition)
					@if($lastGroup != $selectedRecipePosition->ingredient_group)
						<h5 class="mb-2 mt-2 ml-4"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
					@endif
					<li class="list-group-item">
						@if(!empty($selectedRecipePosition->recipe_variable_amount))
							{{ $selectedRecipePosition->recipe_variable_amount }}
						@else
							<span class="locale-number-format" data-format="quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
						@endif
						{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
						@if($selectedRecipePosition->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
						<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($selectedRecipeSubRecipesPositions, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2)) }} @endif</span>

						@if(!empty($selectedRecipePosition->note))
						<div class="text-muted">{!! nl2br($selectedRecipePosition->note) !!}</div>
						@endif
					</li>
					@php $lastGroup = $selectedRecipePosition->ingredient_group; @endphp
					@endforeach
				</ul>
				@endif
				@if(!empty($selectedRecipeSubRecipe->description))
				<div class="card-body">
					<h5>{{ $__t('Preparation') }}</h5>
					{!! $selectedRecipeSubRecipe->description !!}
				</div>
				@endif
			@endforeach

			<!-- Selected recipe -->
			@if(!empty($selectedRecipe->picture_file_name))
				<p class="w-75 mx-auto"><img src="{{ $U('/api/files/recipepictures/' . base64_encode($selectedRecipe->picture_file_name)) }}" class="img-fluid img-thumbnail"></p>
			@endif

			@if($selectedRecipePositionsResolved->count() > 0)
			<div class="card-body">
				<h5 class="mb-0">{{ $__t('Ingredients') }}</h5>
			</div>
			<ul class="list-group list-group-flush">
				@php $lastGroup = 'undefined'; @endphp
				@foreach($selectedRecipePositionsResolved as $selectedRecipePosition)
				@if($lastGroup != $selectedRecipePosition->ingredient_group)
					<h5 class="mb-2 mt-2 ml-4"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
				@endif
				<li class="list-group-item">
					@if(!empty($selectedRecipePosition->recipe_variable_amount))
						{{ $selectedRecipePosition->recipe_variable_amount }}
					@else
						<span class="locale-number-format" data-format="quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
					@endif
					{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $selectedRecipePosition->qu_id)->name_plural) }} {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
					@if($selectedRecipePosition->need_fulfilled == 1)<i class="fas fa-check text-success"></i>@elseif($selectedRecipePosition->need_fulfilled_with_shopping_list == 1)<i class="fas fa-exclamation text-warning"></i>@else<i class="fas fa-times text-danger"></i>@endif
					<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->missing_amount, 2), round(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->amount_on_shopping_list, 2)) }} @endif</span>

					@if(!empty($selectedRecipePosition->note))
					<div class="text-muted">{!! nl2br($selectedRecipePosition->note) !!}</div>
					@endif
				</li>
				@php $lastGroup = $selectedRecipePosition->ingredient_group; @endphp
				@endforeach
			</ul>
			@endif
			@if(!empty($selectedRecipe->description))
			<div class="card-body">
				<h5>{{ $__t('Preparation') }}</h5>
				{!! $selectedRecipe->description !!}
			</div>
			@endif
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
