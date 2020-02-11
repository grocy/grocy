<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;
use \Grocy\Services\StockService;
use \Grocy\Services\UserfieldsService;

class RecipesController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->RecipesService = new RecipesService();
		$this->StockService = new StockService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $RecipesService;
	protected $StockService;
	protected $UserfieldsService;

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name');
		$recipesResolved = $this->RecipesService->GetRecipesResolved();

		$selectedRecipe = null;
		$selectedRecipePositionsResolved = null;
		if (isset($request->getQueryParams()['recipe']))
		{
			$selectedRecipe = $this->Database->recipes($request->getQueryParams()['recipe']);
			$selectedRecipePositionsResolved = $this->Database->recipes_pos_resolved()->where('recipe_id = :1 AND is_nested_recipe_pos = 0', $request->getQueryParams()['recipe'])->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC');
		}
		else
		{
			foreach ($recipes as $recipe)
			{
				$selectedRecipe = $recipe;
				$selectedRecipePositionsResolved = $this->Database->recipes_pos_resolved()->where('recipe_id = :1 AND is_nested_recipe_pos = 0', $recipe->id)->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC');
				break;
			}
		}

		$selectedRecipeSubRecipes = $this->Database->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name')->fetchAll();
		$selectedRecipeSubRecipesPositions = $this->Database->recipes_pos_resolved()->where('recipe_id = :1', $selectedRecipe->id)->orderBy('ingredient_group', 'ASC', 'product_group', 'ASC')->fetchAll();

		$includedRecipeIdsAbsolute = array();
		$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
		foreach($selectedRecipeSubRecipes as $subRecipe)
		{
			$includedRecipeIdsAbsolute[] = $subRecipe->id;
		}

		return $this->View->render($response, 'recipes', [
			'recipes' => $recipes,
			'recipesResolved' => $recipesResolved,
			'recipePositionsResolved' => $this->Database->recipes_pos_resolved()->where('recipe_type', RecipesService::RECIPE_TYPE_NORMAL),
			'selectedRecipe' => $selectedRecipe,
			'selectedRecipePositionsResolved' => $selectedRecipePositionsResolved,
			'products' => $this->Database->products(),
			'quantityUnits' => $this->Database->quantity_units(),
			'selectedRecipeSubRecipes' => $selectedRecipeSubRecipes,
			'selectedRecipeSubRecipesPositions' => $selectedRecipeSubRecipesPositions,
			'includedRecipeIdsAbsolute' => $includedRecipeIdsAbsolute,
			'selectedRecipeTotalCosts' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->costs,
			'selectedRecipeTotalCalories' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->calories,
			'userfields' => $this->UserfieldsService->GetFields('recipes'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('recipes'),
			'quantityUnitConversionsResolved' => $this->Database->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipeEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipeId = $args['recipeId'];
		if ($recipeId  == 'new')
		{
			$newRecipe = $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->createRow(array(
				'name' => $this->LocalizationService->__t('New recipe')
			));
			$newRecipe->save();

			$recipeId = $this->Database->lastInsertId();
		}
		
		return $this->View->render($response, 'recipeform', [
			'recipe' =>  $this->Database->recipes($recipeId),
			'recipePositions' =>  $this->Database->recipes_pos()->where('recipe_id', $recipeId),
			'mode' => 'edit',
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units(),
			'recipePositionsResolved' => $this->RecipesService->GetRecipesPosResolved(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved(),
			'recipes' =>  $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name'),
			'recipeNestings' =>  $this->Database->recipes_nestings()->where('recipe_id', $recipeId),
			'userfields' => $this->UserfieldsService->GetFields('recipes'),
			'quantityUnitConversionsResolved' => $this->Database->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipePosEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['recipePosId'] == 'new')
		{
			return $this->View->render($response, 'recipeposform', [
				'mode' => 'create',
				'recipe' => $this->Database->recipes($args['recipeId']),
				'recipePos' => new \stdClass(),
				'products' => $this->Database->products()->orderBy('name'),
				'quantityUnits' => $this->Database->quantity_units()->orderBy('name'),
				'quantityUnitConversionsResolved' => $this->Database->quantity_unit_conversions_resolved()
			]);
		}
		else
		{
			return $this->View->render($response, 'recipeposform', [
				'mode' => 'edit',
				'recipe' =>  $this->Database->recipes($args['recipeId']),
				'recipePos' => $this->Database->recipes_pos($args['recipePosId']),
				'products' => $this->Database->products()->orderBy('name'),
				'quantityUnits' => $this->Database->quantity_units()->orderBy('name'),
				'quantityUnitConversionsResolved' => $this->Database->quantity_unit_conversions_resolved()
			]);
		}
	}

	public function RecipesSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'recipessettings');
	}

	public function MealPlan(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$recipes = $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll();
		
		$events = array();
		foreach($this->Database->meal_plan() as $mealPlanEntry)
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
				$productDetails = $this->StockService->GetProductDetails($mealPlanEntry['product_id']);
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

		return $this->View->render($response, 'mealplan', [
			'fullcalendarEventSources' => $events,
			'recipes' => $recipes,
			'internalRecipes' => $this->Database->recipes()->whereNot('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved(),
			'products' => $this->Database->products()->orderBy('name'),
			'quantityUnits' => $this->Database->quantity_units()->orderBy('name'),
			'quantityUnitConversionsResolved' => $this->Database->quantity_unit_conversions_resolved()
		]);
	}
}
