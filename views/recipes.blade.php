@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Recipes'))

@push('pageStyles')
<style>
	.card-img-top {
		max-height: 250px !important;
		object-fit: cover !important;
	}

	@media (min-width: 576px) {
		.card-columns {
			column-count: 1;
		}
	}

	@media (min-width: 768px) {
		.card-columns {
			column-count: 2;
		}
	}

	@media (min-width: 1200px) {
		.card-columns {
			column-count: 2;
		}
	}
</style>
@endpush

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="row">
	<div class="@if(boolval($userSettings['recipes_show_list_side_by_side']) || $embedded) col-12 col-md-6 @else col @endif d-print-none">
		<div class="title-related-links border-bottom mb-2 py-1">
			<h2 class="title">@yield('title')</h2>
			<div class="float-right @if($embedded) pr-5 @endif">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fa-solid fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fa-solid fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/recipe/new') }}">
					{{ $__t('Add') }}
				</a>
			</div>
		</div>

		<div class="row collapse d-md-flex"
			id="table-filter-row">
			<div class="col-12 col-md-5">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="fa-solid fa-search"></i></span>
					</div>
					<input type="text"
						id="search"
						class="form-control"
						placeholder="{{ $__t('Search') }}">
				</div>
			</div>

			<div class="col-12 col-md-5">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
					</div>
					<select class="custom-control custom-select"
						id="status-filter">
						<option value="all">{{ $__t('All') }}</option>
						<option value="Xenoughinstock">{{ $__t('Enough in stock') }}</option>
						<option value="enoughinstockwithshoppinglist">{{ $__t('Not enough in stock, but already on the shopping list') }}</option>
						<option value="notenoughinstock">{{ $__t('Not enough in stock') }}</option>
					</select>
				</div>
			</div>

			<div class="col">
				<div class="float-right mt-1">
					<button id="clear-filter-button"
						class="btn btn-sm btn-outline-info"
						data-toggle="tooltip"
						title="{{ $__t('Clear filter') }}">
						<i class="fa-solid fa-filter-circle-xmark"></i>
					</button>
				</div>
			</div>
		</div>

		<ul class="nav nav-tabs grocy-tabs">
			<li class="nav-item">
				<a class="nav-link active"
					id="list-tab"
					data-toggle="tab"
					href="#list">{{ $__t('List') }}</a>
			</li>
			<li class="nav-item">
				<a class="nav-link"
					id="gallery-tab"
					data-toggle="tab"
					href="#gallery">{{ $__t('Gallery') }}</a>
			</li>
		</ul>

		<div class="tab-content grocy-tabs">
			<div class="tab-pane show active"
				id="list">
				<table id="recipes-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
									data-toggle="tooltip"
									title="{{ $__t('Table options') }}"
									data-table-selector="#recipes-table"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th>{{ $__t('Name') }}</th>
							<th class="allow-grouping">{{ $__t('Desired servings') }}</th>
							<th class="allow-grouping">
								{{ $__t('Due score') }}
								<i class="fa-solid fa-question-circle text-muted small"
									data-toggle="tooltip"
									data-trigger="hover click"
									title="{{ $__t('The higher this number is, the more ingredients currently in stock are due soon, overdue or already expired') }}"></i>
							</th>
							<th data-shadow-rowgroup-column="8"
								class="@if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif allow-grouping">{{ $__t('Requirements fulfilled') }}</th>
							<th class="d-none">Hidden status for sorting of "Requirements fulfilled" column</th>
							<th class="d-none">Hidden status for filtering by status</th>
							<th class="d-none">Hidden recipe ingredient product names</th>
							<th class="d-none">Hidden status for grouping by status</th>

							@include('components.userfields_thead', array(
							'userfields' => $userfields
							))

						</tr>
					</thead>
					<tbody class="d-none">
						@foreach($recipes as $recipe)
						<tr id="recipe-row-{{ $recipe->id }}"
							data-recipe-id="{{ $recipe->id }}">
							<td class="fit-content border-right">
								<a class="btn btn-info btn-sm hide-when-embedded hide-on-fullscreen-card recipe-edit-button"
									href="{{ $U('/recipe/') }}{{ $recipe->id }}"
									data-toggle="tooltip"
									title="{{ $__t('Edit this item') }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<div class="dropdown d-inline-block">
									<button class="btn btn-sm btn-light text-secondary"
										type="button"
										data-toggle="dropdown">
										<i class="fa-solid fa-ellipsis-v"></i>
									</button>
									<div class="table-inline-menu dropdown-menu dropdown-menu-right hide-on-fullscreen-card hide-when-embedded">
										<a class="dropdown-item add-to-mealplan-button"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}">
											<span class="dropdown-item-text">{{ $__t('Add to meal plan') }}</span>
										</a>
										<a class="dropdown-item recipe-delete"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}"
											data-recipe-name="{{ $recipe->name }}">
											<span class="dropdown-item-text">{{ $__t('Delete this item') }}</span>
										</a>
										<a class="dropdown-item recipe-copy"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}">
											<span class="dropdown-item-text">{{ $__t('Copy recipe') }}</span>
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item"
											type="button"
											href="{{ $U('/recipe/' . $recipe->id . '/grocycode?download=true') }}">
											<span class="dropdown-item-text">{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Download %s Grocycode', $__t('Recipe'))) !!}</span>
										</a>
										@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
										<a class="dropdown-item recipe-grocycode-label-print"
											data-recipe-id="{{ $recipe->id }}"
											type="button"
											href="#">
											<span class="dropdown-item-text">{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Print %s Grocycode on label printer', $__t('Recipe'))) !!}</span>
										</a>
										@endif
									</div>
								</div>
							</td>
							<td>
								{{ $recipe->name }}
							</td>
							<td>
								{{ $recipe->desired_servings }}
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->due_score }}
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning"></i>@else<i class="fa-solid fa-times text-danger"></i>@endif
								<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
							</td>
							<td class="d-none">
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count }}
							</td>
							<td class="d-none">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) Xenoughinstock @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) enoughinstockwithshoppinglist @else notenoughinstock @endif
							</td>
							<td class="d-none">
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->product_names_comma_separated }}
							</td>
							<td class="d-none">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) {{ $__t('Not enough in stock, but already on the shopping list') }} @else {{ $__t('Not enough in stock') }} @endif
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

			<div class="tab-pane show"
				id="gallery">
				<div class="card-columns no-gutters mt-1">
					@foreach($recipes as $recipe)
					<div class="cursor-link recipe-gallery-item @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) recipe-enoughinstock @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) recipe-enoughinstockwithshoppinglist @else recipe-notenoughinstock @endif"
						data-recipe-id="{{ $recipe->id }}"
						href="#">
						<div id="RecipeGalleryCard-{{ $recipe->id }}"
							class="card recipe-card">
							@if(!empty($recipe->picture_file_name))
							<img src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
								class="card-img-top"
								loading="lazy">
							@endif
							<div class="card-body text-center">
								<h5 class="card-title mb-1">{{ $recipe->name }}</h5>
								<span class="card-title-search d-none">
									{{ $recipe->name }}
									{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->product_names_comma_separated }}
								</span>
								<p class="card-text">
									@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning"></i>@else<i class="fa-solid fa-times text-danger"></i>@endif
									<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
								</p>
								<p class="card-text mt-2">
									<a class="btn btn-xs btn-outline-danger hide-when-embedded hide-on-fullscreen-card recipe-delete"
										href="#"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}"
										data-toggle="tooltip"
										title="{{ $__t('Delete this item') }}">
										<i class="fa-solid fa-trash"></i>
									</a>
									<a class="btn btn-outline-info btn-xs hide-when-embedded hide-on-fullscreen-card recipe-edit-button"
										href="{{ $U('/recipe/') }}{{ $recipe->id }}"
										data-toggle="tooltip"
										title="{{ $__t('Edit this item') }}">
										<i class="fa-solid fa-edit"></i>
									</a>
								</p>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	@if($selectedRecipe !== null && (boolval($userSettings['recipes_show_list_side_by_side']) || $embedded))
	@php
	$allRecipes = $selectedRecipeSubRecipes;
	array_unshift($allRecipes, $selectedRecipe);
	@endphp
	<div class="col-12 col-md-6 print-view">
		<div id="selectedRecipeCard"
			class="card grocy-card">
			@if(count($allRecipes) > 1)
			<div class="card-header card-header-fullscreen mb-1 pt-0 d-print-none">
				<ul class="nav nav-tabs grocy-tabs card-header-tabs">
					@foreach($allRecipes as $index=>$recipe)
					<li class="nav-item">
						<a class="nav-link @if($index == 0) active @endif"
							data-toggle="tab"
							href="#recipe-{{ $index + 1 }}">{{ $recipe->name }}</a>
					</li>
					@endforeach
				</ul>
			</div>
			@endif

			<div class="tab-content grocy-tabs print break">
				@foreach($allRecipes as $index=>$recipe)
				<div class="tab-pane @if($index == 0) active @endif"
					id="recipe-{{ $index + 1 }}"
					role="tabpanel">
					@if(!empty($recipe->picture_file_name))
					<img class="card-img-top"
						src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture') }}"
						loading="lazy">
					@endif
					<div class="card-body">
						<div class="shadow p-4 mb-5 bg-white rounded mt-n5 d-print-none @if(empty($recipe->picture_file_name)) d-none @endif">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="card-title mb-0">{{ $recipe->name }}</h3>
								<div class="card-icons d-flex flex-wrap justify-content-end flex-shrink-1">
									<a class="btn @if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif recipe-consume"
										href="#"
										data-toggle="tooltip"
										title="{{ $__t('Consume all ingredients needed by this recipe') }}"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}">
										<i class="fa-solid fa-utensils"></i>
									</a>
									<a class="btn @if(!GROCY_FEATURE_FLAG_SHOPPINGLIST) d-none @endif recipe-shopping-list @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) disabled @endif"
										href="#"
										data-toggle="tooltip"
										title="{{ $__t('Put missing products on shopping list') }}"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}">
										<i class="fa-solid fa-cart-plus"></i>
									</a>
									<a class="btn recipe-fullscreen hide-when-embedded"
										id="selectedRecipeToggleFullscreenButton"
										href="#"
										data-toggle="tooltip"
										title="{{ $__t('Expand to fullscreen') }}">
										<i class="fa-solid fa-expand-arrows-alt"></i>
									</a>
									<a class="btn recipe-print"
										href="#"
										data-toggle="tooltip"
										title="{{ $__t('Print') }}">
										<i class="fa-solid fa-print"></i>
									</a>
								</div>
							</div>
						</div>

						<div class="mb-4 @if(!empty($recipe->picture_file_name)) d-none @else d-flex @endif d-print-block justify-content-between align-items-center">
							<h1 class="card-title mb-0">{{ $recipe->name }}</h1>
							<div class="card-icons d-flex flex-wrap justify-content-end flex-shrink-1 d-print-none">
								<a class="btn recipe-consume"
									href="#"
									data-toggle="tooltip"
									title="{{ $__t('Consume all ingredients needed by this recipe') }}"
									data-recipe-id="{{ $recipe->id }}"
									data-recipe-name="{{ $recipe->name }}">
									<i class="fa-solid fa-utensils"></i>
								</a>
								<a class="btn recipe-shopping-list @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) disabled @endif"
									href="#"
									data-toggle="tooltip"
									title="{{ $__t('Put missing products on shopping list') }}"
									data-recipe-id="{{ $recipe->id }}"
									data-recipe-name="{{ $recipe->name }}">
									<i class="fa-solid fa-cart-plus"></i>
								</a>
								<a class=" btn recipe-fullscreen hide-when-embedded"
									href="#"
									data-toggle="tooltip"
									title="{{ $__t('Expand to fullscreen') }}">
									<i class="fa-solid fa-expand-arrows-alt"></i>
								</a>
								<a class="btn recipe-print PrintRecipe"
									href="#"
									data-toggle="tooltip"
									title="{{ $__t('Print') }}">
									<i class="fa-solid fa-print"></i>
								</a>
							</div>
						</div>

						@php
						$calories = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->calories;
						$costs = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->costs;
						@endphp

						<div class="row ml-1">
							@if(!empty($calories) && $calories > 0)
							<div class="col-4">
								<label>{{ GROCY_ENERGY_UNIT }}</label>&nbsp;
								<i class="fa-solid fa-question-circle text-muted d-print-none"
									data-toggle="tooltip"
									data-trigger="hover click"
									title="{{ $__t('per serving') }}"></i>
								<h3 class="locale-number locale-number-generic pt-0">{{ $calories }}</h3>
							</div>
							@endif
							@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
							<div class="col-4">
								<label>{{ $__t('Costs') }}&nbsp;
									<i class="fa-solid fa-question-circle text-muted d-print-none"
										data-toggle="tooltip"
										data-trigger="hover click"
										title="{{ $__t('Based on the prices of the default consume rule (Opened first, then first due first, then first in first out) for in stock ingredients and on the last price for missing ones') }}"></i>
								</label>
								<h3>
									<span class="locale-number locale-number-currency pt-0">{{ $costs }}</span>
									@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->prices_incomplete)
									<i class="small fa-solid fa-exclamation text-danger"
										data-toggle="tooltip"
										data-trigger="hover click"
										title="{{ $__t('No price information is available for at least one ingredient') }}"></i>
									@endif
								</h3>
							</div>
							@endif

							@if($index == 0)
							<div class="col-4 d-print-none">
								@include('components.numberpicker', array(
								'id' => 'servings-scale',
								'label' => 'Desired servings',
								'min' => $DEFAULT_MIN_AMOUNT,
								'decimals' => $userSettings['stock_decimal_places_amounts'],
								'value' => $recipe->desired_servings,
								'additionalAttributes' => 'data-recipe-id="' . $recipe->id . '"',
								'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
								))
							</div>
							@endif
						</div>

						@php
						$recipePositionsFiltered = FindAllObjectsInArrayByPropertyValue($allRecipePositions[$recipe->id], 'recipe_id', $recipe->id);
						@endphp

						<ul class="nav nav-tabs grocy-tabs mb-3 d-print-none hide-on-fullscreen-card"
							role="tablist">
							@if(count($recipePositionsFiltered) > 0)
							<li class="nav-item">
								<a class="nav-link active"
									data-toggle="tab"
									href="#ingredients-{{ $index }}"
									role="tab">{{ $__t('Ingredients') }}</a>
							</li>
							@endif
							@if(!empty($recipe->description))
							<li class="nav-item">
								<a class="nav-link @if(count($recipePositionsFiltered) == 0) active @endif"
									data-toggle="tab"
									href="#prep-{{ $index }}"
									role="tab">{{ $__t('Preparation') }}</a>
							</li>
							@endif
						</ul>

						<div class="tab-content grocy-tabs p-2 print recipe-content-container">
							@if(count($recipePositionsFiltered) > 0)
							<div class="tab-pane active ingredients"
								id="ingredients-{{ $index }}"
								role="tabpanel">
								<div class="mb-2 d-none d-print-block recipe-headline">
									<h3 class="mb-0">{{ $__t('Ingredients') }}</h3>
								</div>
								<ul class="list-group list-group-flush mb-5">
									@php
									$lastIngredientGroup = 'undefined';
									$lastProductGroup = 'undefined';
									$hasIngredientGroups = false;
									$hasProductGroups = false;
									@endphp
									@foreach($recipePositionsFiltered as $selectedRecipePosition)
									@if($lastIngredientGroup != $selectedRecipePosition->ingredient_group && !empty($selectedRecipePosition->ingredient_group))
									@php $hasIngredientGroups = true; @endphp
									<h5 class="mb-2 mt-2 ml-1"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
									@endif
									@if(boolval($userSettings['recipe_ingredients_group_by_product_group']) && $lastProductGroup != $selectedRecipePosition->product_group && !empty($selectedRecipePosition->product_group))
									@php $hasProductGroups = true; @endphp
									<h6 class="mb-2 mt-2 @if($hasIngredientGroups) ml-3 @else ml-1 @endif"><strong>{{ $selectedRecipePosition->product_group }}</strong></h6>
									@endif
									<li class="list-group-item px-0 @if($hasIngredientGroups && $hasProductGroups) ml-4 @elseif($hasIngredientGroups || $hasProductGroups) ml-2 @else ml-0 @endif">
										@if($selectedRecipePosition->product_active == 0)
										<div class="small text-muted font-italic">{{ $__t('Disabled') }}</div>
										@endif
										@if($userSettings['recipes_show_ingredient_checkbox'])
										<a class="btn btn-light btn-sm ingredient-done-button"
											href="#"
											data-toggle="tooltip"
											data-placement="right"
											title="{{ $__t('Mark this item as done') }}">
											<i class="fa-solid fa-check-circle text-primary"></i>
										</a>
										@endif
										@php
										$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
										$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
										$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
										$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
										if ($productQuConversion && $selectedRecipePosition->only_check_single_unit_in_stock == 0)
										{
										$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
										}
										@endphp
										<span class="productcard-trigger cursor-link @if($selectedRecipePosition->due_score == 20) text-danger @elseif($selectedRecipePosition->due_score == 10) text-secondary @elseif($selectedRecipePosition->due_score == 1) text-warning @endif"
											data-product-id="{{ $selectedRecipePosition->product_id }}">
											@if(!empty($selectedRecipePosition->recipe_variable_amount))
											{{ $selectedRecipePosition->recipe_variable_amount }}
											@else
											<span class="locale-number locale-number-quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
											{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural) }}
											@endif
											{{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
										</span>
										@if(GROCY_FEATURE_FLAG_STOCK)
										<span class="
												d-print-none">
											@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning"></i>@else<i class="fa-solid fa-times text-danger"></i>@endif
											<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} (<span class="locale-number locale-number-quantity-amount">{{ $selectedRecipePosition->stock_amount }}</span> {{ $__n($selectedRecipePosition->stock_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $product->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $product->qu_id_stock)->name_plural) }}) @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round($selectedRecipePosition->missing_amount, 2), round($selectedRecipePosition->amount_on_shopping_list, 2)) }} @endif</span>
										</span>
										@endif
										@if($selectedRecipePosition->product_id != $selectedRecipePosition->product_id_effective)
										<br class="d-print-none">
										<span class="productcard-trigger cursor-link text-muted d-print-none"
											data-product-id="{{ $selectedRecipePosition->product_id_effective }}"
											data-toggle="tooltip"
											data-trigger="hover click"
											title="{{ $__t('The parent product %1$s is currently not in stock, %2$s is the current next sub product based on the default consume rule (Opened first, then first due first, then first in first out)', FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name, FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id_effective)->name) }}">
											<i class="fa-solid fa-exchange-alt"></i> {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id_effective)->name }}
										</span>
										@endif
										@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) <span class="float-right font-italic ml-2 locale-number locale-number-currency">{{ $selectedRecipePosition->costs }}</span> @endif
										<span class="float-right font-italic"><span class="locale-number locale-number-generic">{{ $selectedRecipePosition->calories }}</span> {{ $__t('Calories') }}</span>
										@if(!empty($selectedRecipePosition->recipe_variable_amount))
										<div class="small text-muted font-italic">{{ $__t('Variable amount') }}</div>
										@endif

										@if(!empty($selectedRecipePosition->note))
										<div class="text-muted">{!! nl2br($selectedRecipePosition->note ?? '') !!}</div>
										@endif
									</li>
									@php $lastProductGroup = $selectedRecipePosition->product_group; @endphp
									@php $lastIngredientGroup = $selectedRecipePosition->ingredient_group; @endphp
									@endforeach
								</ul>
							</div>
							@endif
							<div class="tab-pane @if(count($recipePositionsFiltered) == 0) active @endif preparation"
								id="prep-{{ $index }}"
								role="tabpanel">
								<div class="mb-2 d-none d-print-block recipe-headline">
									<h3 class="mb-0">{{ $__t('Preparation') }}</h3>
								</div>
								@if(!empty($recipe->description))
								{!! $recipe->description !!}
								@endif
							</div>
						</div>
					</div>
				</div>

				<div id="missing-recipe-pos-list"
					class="list-group d-none mt-3">
					@foreach($recipePositionsResolved as $recipePos)
					@if(in_array($recipePos->recipe_id, $includedRecipeIdsAbsolute) && $recipePos->missing_amount > 0)
					<a href="#"
						class="list-group-item list-group-item-action list-group-item-primary missing-recipe-pos-select-button">
						<div class="form-check form-check-inline">
							<input class="form-check-input missing-recipe-pos-product-checkbox"
								type="checkbox"
								data-product-id="{{ $recipePos->product_id }}"
								checked>
						</div>
						{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name }}
					</a>
					@endif
					@endforeach
				</div>
				@endforeach
			</div>
		</div>
	</div>
	@endif
</div>

<div class="modal fade"
	id="add-to-mealplan-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title w-100">
					<span>{{ $__t('Add meal plan entry') }}</span>
					<span class="text-muted float-right">{{ $__t('Recipe') }}</span>
				</h4>
			</div>
			<div class="modal-body">
				<form id="add-to-mealplan-form"
					novalidate>

					@include('components.datetimepicker', array(
					'id' => 'day',
					'label' => 'Day',
					'format' => 'YYYY-MM-DD',
					'initWithNow' => false,
					'limitEndToNow' => false,
					'limitStartToNow' => false,
					'isRequired' => true,
					'additionalCssClasses' => 'date-only-datetimepicker',
					'invalidFeedback' => $__t('A date is required')
					))

					@include('components.recipepicker', array(
					'recipes' => $recipes,
					'isRequired' => true,
					'nextInputSelector' => '#recipe_servings'
					))

					@include('components.numberpicker', array(
					'id' => 'recipe_servings',
					'label' => 'Servings',
					'min' => $DEFAULT_MIN_AMOUNT,
					'decimals' => $userSettings['stock_decimal_places_amounts'],
					'value' => '1',
					'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
					))

					<div class="form-group">
						<label for="section_id">{{ $__t('Section') }}</label>
						<select class="custom-control custom-select"
							id="section_id"
							name="section_id"
							required>
							@foreach($mealplanSections as $mealplanSection)
							<option value="{{ $mealplanSection->id }}">{{ $mealplanSection->name }}</option>
							@endforeach
						</select>
					</div>

					<input type="hidden"
						name="type"
						value="recipe">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="save-add-to-mealplan-button"
					class="btn btn-success">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>

@include('components.productcard', [
'asModal' => true
])
@stop
