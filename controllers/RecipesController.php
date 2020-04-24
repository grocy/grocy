<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;

class RecipesController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name');
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

		$selectedRecipePositionsResolved = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id = :1 AND is_nested_recipe_pos = 0', $selectedRecipe->id)->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC');

		$renderArray = [
			'recipes' => $recipes,
			'recipesResolved' => $recipesResolved,
			'recipePositionsResolved' => $this->getDatabase()->recipes_pos_resolved()->where('recipe_type', RecipesService::RECIPE_TYPE_NORMAL),
			'selectedRecipe' => $selectedRecipe,
			'selectedRecipePositionsResolved' => $selectedRecipePositionsResolved,
			'products' => $this->getDatabase()->products(),
			'quantityUnits' => $this->getDatabase()->quantity_units(),
			'userfields' => $this->getUserfieldsService()->GetFields('recipes'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved(),
			'selectedRecipeTotalCosts' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->costs,
			'selectedRecipeTotalCalories' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->calories
		];

		if ($selectedRecipe)
		{
			$selectedRecipeSubRecipes = $this->getDatabase()->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name')->fetchAll();
			
			$includedRecipeIdsAbsolute = array();
			$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
			foreach($selectedRecipeSubRecipes as $subRecipe)
			{
				$includedRecipeIdsAbsolute[] = $subRecipe->id;
			}

			$allRecipePositions = array();
			foreach($includedRecipeIdsAbsolute as $id)
			{
				$allRecipePositions[$id] = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id = :1 AND is_nested_recipe_pos = 0', $id)->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC');
			}

			$renderArray['selectedRecipeSubRecipes'] = $selectedRecipeSubRecipes;
			$renderArray['includedRecipeIdsAbsolute'] = $includedRecipeIdsAbsolute;
			$renderArray['allRecipePositions'] = $allRecipePositions;
		}

		return $this->renderPage($response, 'recipes', $renderArray);
	}

	public function RecipeEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipeId = $args['recipeId'];

		return $this->renderPage($response, 'recipeform', [
			'recipe' =>  $this->getDatabase()->recipes($recipeId),
			'recipePositions' =>  $this->getDatabase()->recipes_pos()->where('recipe_id', $recipeId),
			'mode' => $recipeId  == 'new' ? "create" : "edit",
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityunits' => $this->getDatabase()->quantity_units(),
			'recipePositionsResolved' => $this->getRecipesService()->GetRecipesPosResolved(),
			'recipesResolved' => $this->getRecipesService()->GetRecipesResolved(),
			'recipes' =>  $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name'),
			'recipeNestings' =>  $this->getDatabase()->recipes_nestings()->where('recipe_id', $recipeId),
			'userfields' => $this->getUserfieldsService()->GetFields('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipePosEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['recipePosId'] == 'new')
		{
			return $this->renderPage($response, 'recipeposform', [
				'mode' => 'create',
				'recipe' => $this->getDatabase()->recipes($args['recipeId']),
				'recipePos' => new \stdClass(),
				'products' => $this->getDatabase()->products()->orderBy('name'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
			]);
		}
		else
		{
			return $this->renderPage($response, 'recipeposform', [
				'mode' => 'edit',
				'recipe' =>  $this->getDatabase()->recipes($args['recipeId']),
				'recipePos' => $this->getDatabase()->recipes_pos($args['recipePosId']),
				'products' => $this->getDatabase()->products()->orderBy('name'),
				'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name'),
				'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
			]);
		}
	}

	public function RecipesSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'recipessettings');
	}

	public function MealPlan(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll();

		$events = array();
		foreach($this->getDatabase()->meal_plan() as $mealPlanEntry)
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

			$events[] = array(
				'id' => $mealPlanEntry['id'],
				'title' => $title,
				'start' => $mealPlanEntry['day'],
				'date_format' => 'date',
				'recipe' => json_encode($recipe),
				'mealPlanEntry' => json_encode($mealPlanEntry),
				'type' => $mealPlanEntry['type'],
				'productDetails' => json_encode($productDetails)
			);
		}

		return $this->renderPage($response, 'mealplan', [
			'fullcalendarEventSources' => $events,
			'recipes' => $recipes,
			'internalRecipes' => $this->getDatabase()->recipes()->whereNot('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll(),
			'recipesResolved' => $this->getRecipesService()->GetRecipesResolved(),
			'products' => $this->getDatabase()->products()->orderBy('name'),
			'quantityUnits' => $this->getDatabase()->quantity_units()->orderBy('name'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}
}
