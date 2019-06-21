<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;
use \Grocy\Services\UserfieldsService;

class RecipesController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->RecipesService = new RecipesService();
		$this->UserfieldsService = new UserfieldsService();
	}

	protected $RecipesService;
	protected $UserfieldsService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if (isset($request->getQueryParams()['include-internal']))
		{
			$recipes = $this->Database->recipes()->orderBy('name');
		}
		else
		{
			$recipes = $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name');
		}
		$recipesResolved = $this->RecipesService->GetRecipesResolved();

		$selectedRecipe = null;
		$selectedRecipePositionsResolved = null;
		if (isset($request->getQueryParams()['recipe']))
		{
			$selectedRecipe = $this->Database->recipes($request->getQueryParams()['recipe']);
			$selectedRecipePositionsResolved = $this->Database->recipes_pos_resolved()->where('recipe_id', $request->getQueryParams()['recipe'])->orderBy('ingredient_group');
		}
		else
		{
			foreach ($recipes as $recipe)
			{
				$selectedRecipe = $recipe;
				$selectedRecipePositionsResolved = $this->Database->recipes_pos_resolved()->where('recipe_id', $recipe->id)->orderBy('ingredient_group');
				break;
			}
		}

		$selectedRecipeSubRecipes = $this->Database->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name')->fetchAll();
		$selectedRecipeSubRecipesPositions = $this->Database->recipes_pos_resolved()->where('recipe_id = :1', $selectedRecipe->id)->orderBy('ingredient_group')->fetchAll();

		$includedRecipeIdsAbsolute = array();
		$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
		foreach($selectedRecipeSubRecipes as $subRecipe)
		{
			$includedRecipeIdsAbsolute[] = $subRecipe->id;
		}

		return $this->AppContainer->view->render($response, 'recipes', [
			'recipes' => $recipes,
			'recipesResolved' => $recipesResolved,
			'recipePositionsResolved' => $this->Database->recipes_pos_resolved(),
			'selectedRecipe' => $selectedRecipe,
			'selectedRecipePositionsResolved' => $selectedRecipePositionsResolved,
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'selectedRecipeSubRecipes' => $selectedRecipeSubRecipes,
			'selectedRecipeSubRecipesPositions' => $selectedRecipeSubRecipesPositions,
			'includedRecipeIdsAbsolute' => $includedRecipeIdsAbsolute,
			'selectedRecipeTotalCosts' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->costs,
			'userfields' => $this->UserfieldsService->GetFields('recipes'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('recipes')
		]);
	}

	public function RecipeEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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
		
		return $this->AppContainer->view->render($response, 'recipeform', [
			'recipe' =>  $this->Database->recipes($recipeId),
			'recipePositions' =>  $this->Database->recipes_pos()->where('recipe_id', $recipeId),
			'mode' => 'edit',
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'recipePositionsResolved' => $this->RecipesService->GetRecipesPosResolved(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved(),
			'recipes' =>  $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name'),
			'recipeNestings' =>  $this->Database->recipes_nestings()->where('recipe_id', $recipeId),
			'userfields' => $this->UserfieldsService->GetFields('recipes')
		]);
	}

	public function RecipePosEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['recipePosId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'recipeposform', [
				'mode' => 'create',
				'recipe' => $this->Database->recipes($args['recipeId']),
				'products' => $this->Database->products()->orderBy('name'),
				'quantityUnits' => $this->Database->quantity_units()->orderBy('name')
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'recipeposform', [
				'mode' => 'edit',
				'recipe' =>  $this->Database->recipes($args['recipeId']),
				'recipePos' => $this->Database->recipes_pos($args['recipePosId']),
				'products' => $this->Database->products()->orderBy('name'),
				'quantityUnits' => $this->Database->quantity_units()->orderBy('name')
			]);
		}
	}

	public function MealPlan(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$recipes = $this->Database->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll();

		$events = array();
		foreach($this->Database->meal_plan() as $mealPlanEntry)
		{
			$events[] = array(
				'id' => $mealPlanEntry['id'],
				'title' => FindObjectInArrayByPropertyValue($recipes, 'id', $mealPlanEntry['recipe_id'])->name,
				'start' => $mealPlanEntry['day'],
				'date_format' => 'date',
				'recipe' => json_encode(FindObjectInArrayByPropertyValue($recipes, 'id', $mealPlanEntry['recipe_id'])),
				'mealPlanEntry' => json_encode($mealPlanEntry)
			);
		}

		return $this->AppContainer->view->render($response, 'mealplan', [
			'fullcalendarEventSources' => $events,
			'recipes' => $recipes,
			'internalRecipes' => $this->Database->recipes()->whereNot('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved()
		]);
	}
}
