<?php

namespace Grocy\Controllers;

use Grocy\Services\RecipesService;

class RecipesController extends BaseController
{
	public function MealPlan(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll();

		$events = [];

		foreach ($this->getDatabase()->meal_plan() as $mealPlanEntry)
		{
			$recipe = FindObjectInArrayByPropertyValue($recipes, 'id', $mealPlanEntry['recipe_id']);
			$title = '';

			if ($recipe !== null)
			{
				$title = $recipe->name;
			}

			$productDetails = null;

			if ($mealPlanEntry['product_id'] !== null)
			{
				$productDetails = $this->getStockService()->GetProductDetails($mealPlanEntry['product_id']);
			}

			$events[] = [
				'id' => $mealPlanEntry['id'],
				'title' => $title,
				'start' => $mealPlanEntry['day'],
				'date_format' => 'date',
				'recipe' => json_encode($recipe),
				'mealPlanEntry' => json_encode($mealPlanEntry),
				'type' => $mealPlanEntry['type'],
				'productDetails' => json_encode($productDetails)
			];
		}

		return $this->renderPage($request, $response, 'mealplan', [
			'fullcalendarEventSources' => $events,
			'recipes' => $recipes,
			'internalRecipes' => $this->getDatabase()->recipes()->whereNot('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll(),
			'recipesResolved' => $this->getRecipesService()->GetRecipesResolved(),
			'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name', 'COLLATE NOCASE');
		$recipesResolved = $this->getRecipesService()->GetRecipesResolved();

		$selectedRecipe = null;

		if (isset($request->getQueryParams()['recipe']))
		{
			$selectedRecipe = $this->getDatabase()->recipes($request->getQueryParams()['recipe']);
		}
		else
		{
			foreach ($recipes as $recipe)
			{
				$selectedRecipe = $recipe;
				break;
			}
		}

		$totalCosts = null;
		$totalCalories = null;
		if ($selectedRecipe)
		{
			$totalCosts = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->costs;
			$totalCalories = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->calories;
		}

		$renderArray = [
			'recipes' => $recipes,
			'recipesResolved' => $recipesResolved,
			'recipePositionsResolved' => $this->getDatabase()->recipes_pos_resolved()->where('recipe_type', RecipesService::RECIPE_TYPE_NORMAL),
			'selectedRecipe' => $selectedRecipe,
			'products' => $this->getDatabase()->products(),
			'quantityUnits' => $this->getDatabase()->quantity_units(),
			'userfields' => $this->getUserfieldsService()->GetFields('recipes'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
			'selectedRecipeTotalCosts' => $totalCosts,
			'selectedRecipeTotalCalories' => $totalCalories
		];

		if ($selectedRecipe)
		{
			$selectedRecipeSubRecipes = $this->getDatabase()->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name', 'COLLATE NOCASE')->fetchAll();

			$includedRecipeIdsAbsolute = [];
			$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
			foreach ($selectedRecipeSubRecipes as $subRecipe)
			{
				$includedRecipeIdsAbsolute[] = $subRecipe->id;
			}

			// TODO: Why not directly use recipes_pos_resolved for all recipe positions here (parent and child)?
			// This view already correctly recolves child recipe amounts...
			$allRecipePositions = [];
			foreach ($includedRecipeIdsAbsolute as $id)
			{
				$allRecipePositions[$id] = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id = :1 AND is_nested_recipe_pos = 0', $id)->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC');
				foreach ($allRecipePositions[$id] as $pos)
				{
					if ($id != $selectedRecipe->id)
					{
						$pos2 = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id = :1  AND recipe_pos_id = :2 AND is_nested_recipe_pos = 1', $selectedRecipe->id, $pos->recipe_pos_id)->fetch();
						$pos->recipe_amount = $pos2->recipe_amount;
						$pos->missing_amount = $pos2->missing_amount;
					}
				}
			}

			$renderArray['selectedRecipeSubRecipes'] = $selectedRecipeSubRecipes;
			$renderArray['includedRecipeIdsAbsolute'] = $includedRecipeIdsAbsolute;
			$renderArray['allRecipePositions'] = $allRecipePositions;
		}

		return $this->renderPage($request, $response, 'recipes', $renderArray);
	}

	public function RecipeEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipeId = $args['recipeId'];

		return $this->renderPage($request, $response, 'recipeform', [
			'recipe' => $this->getDatabase()->recipes($recipeId),
			'recipePositions' => $this->getDatabase()->recipes_pos()->where('recipe_id', $recipeId),
			'mode' => $recipeId == 'new' ? 'create' : 'edit',
			'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE'),
			'quantityunits' => $this->getDatabase()->quantity_units(),
			'recipePositionsResolved' => $this->getRecipesService()->GetRecipesPosResolved(),
			'recipesResolved' => $this->getRecipesService()->GetRecipesResolved(),
			'recipes' => $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name', 'COLLATE NOCASE'),
			'recipeNestings' => $this->getDatabase()->recipes_nestings()->where('recipe_id', $recipeId),
			'userfields' => $this->getUserfieldsService()->GetFields('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipePosEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['recipePosId'] == 'new')
		{
			return $this->renderPage($request, $response, 'recipeposform', [
				'mode' => 'create',
				'recipe' => $this->getDatabase()->recipes($args['recipeId']),
				'recipePos' => new \stdClass(),
				'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
			]);
		}
		else
		{
			return $this->renderPage($request, $response, 'recipeposform', [
				'mode' => 'edit',
				'recipe' => $this->getDatabase()->recipes($args['recipeId']),
				'recipePos' => $this->getDatabase()->recipes_pos($args['recipePosId']),
				'products' => $this->getDatabase()->products()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name', 'COLLATE NOCASE'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
			]);
		}
	}

	public function RecipesSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'recipessettings');
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
