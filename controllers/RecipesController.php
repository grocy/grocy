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
			$recipes = $this->getDatabase()->recipes()->orderBy('name');
		}
		else
		{
			$recipes = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name');
		}
		$recipesResolved = $this->RecipesService->GetRecipesResolved();

		$selectedRecipe = null;
		$selectedRecipePositionsResolved = null;
		if (isset($request->getQueryParams()['recipe']))
		{
			$selectedRecipe = $this->getDatabase()->recipes($request->getQueryParams()['recipe']);
			$selectedRecipePositionsResolved = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id', $request->getQueryParams()['recipe'])->orderBy('ingredient_group');
		}
		else
		{
			foreach ($recipes as $recipe)
			{
				$selectedRecipe = $recipe;
				$selectedRecipePositionsResolved = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id', $recipe->id)->orderBy('ingredient_group');
				break;
			}
		}

		$selectedRecipeSubRecipes = $this->getDatabase()->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name')->fetchAll();
		$selectedRecipeSubRecipesPositions = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id = :1', $selectedRecipe->id)->orderBy('ingredient_group')->fetchAll();

		$includedRecipeIdsAbsolute = array();
		$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
		foreach($selectedRecipeSubRecipes as $subRecipe)
		{
			$includedRecipeIdsAbsolute[] = $subRecipe->id;
		}

		return $this->renderPage($response, 'recipes', [
			'recipes' => $recipes,
			'recipesResolved' => $recipesResolved,
			'recipePositionsResolved' => $this->getDatabase()->recipes_pos_resolved(),
			'selectedRecipe' => $selectedRecipe,
			'selectedRecipePositionsResolved' => $selectedRecipePositionsResolved,
			'products' => $this->getDatabase()->products(),
			'quantityUnits' => $this->getDatabase()->quantity_units(),
			'selectedRecipeSubRecipes' => $selectedRecipeSubRecipes,
			'selectedRecipeSubRecipesPositions' => $selectedRecipeSubRecipesPositions,
			'includedRecipeIdsAbsolute' => $includedRecipeIdsAbsolute,
			'selectedRecipeTotalCosts' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->costs,
			'selectedRecipeTotalCalories' => FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $selectedRecipe->id)->calories,
			'userfields' => $this->UserfieldsService->GetFields('recipes'),
			'userfieldValues' => $this->UserfieldsService->GetAllValues('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipeEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$recipeId = $args['recipeId'];
		if ($recipeId  == 'new')
		{
			$newRecipe = $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->createRow(array(
				'name' => $this->getLocalizationService()->__t('New recipe')
			));
			$newRecipe->save();

			$recipeId = $this->getDatabase()->lastInsertId();
		}

		return $this->renderPage($response, 'recipeform', [
			'recipe' =>  $this->getDatabase()->recipes($recipeId),
			'recipePositions' =>  $this->getDatabase()->recipes_pos()->where('recipe_id', $recipeId),
			'mode' => 'edit',
			'products' => $this->getDatabase()->products(),
			'quantityunits' => $this->getDatabase()->quantity_units(),
			'recipePositionsResolved' => $this->RecipesService->GetRecipesPosResolved(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved(),
			'recipes' =>  $this->getDatabase()->recipes()->where('type', RecipesService::RECIPE_TYPE_NORMAL)->orderBy('name'),
			'recipeNestings' =>  $this->getDatabase()->recipes_nestings()->where('recipe_id', $recipeId),
			'userfields' => $this->UserfieldsService->GetFields('recipes'),
			'quantityUnitConversionsResolved' => $this->getDatabase()->quantity_unit_conversions_resolved()
		]);
	}

	public function RecipePosEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

	public function MealPlan(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
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

			$events[] = array(
				'id' => $mealPlanEntry['id'],
				'title' => $title,
				'start' => $mealPlanEntry['day'],
				'date_format' => 'date',
				'recipe' => json_encode($recipe),
				'mealPlanEntry' => json_encode($mealPlanEntry)
			);
		}

		return $this->renderPage($response, 'mealplan', [
			'fullcalendarEventSources' => $events,
			'recipes' => $recipes,
			'internalRecipes' => $this->getDatabase()->recipes()->whereNot('type', RecipesService::RECIPE_TYPE_NORMAL)->fetchAll(),
			'recipesResolved' => $this->RecipesService->GetRecipesResolved()
		]);
	}
}
