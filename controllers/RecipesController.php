<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;

class RecipesController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->RecipesService = new RecipesService();
	}

	protected $RecipesService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$recipes = $this->Database->recipes()->orderBy('name');

		$selectedRecipe = null;
		$selectedRecipePositions = null;
		if (isset($request->getQueryParams()['recipe']))
		{
			$selectedRecipe = $this->Database->recipes($request->getQueryParams()['recipe']);
			$selectedRecipePositions = $this->Database->recipes_pos()->where('recipe_id', $request->getQueryParams()['recipe'])->orderBy('ingredient_group');
		}
		else
		{
			foreach ($recipes as $recipe)
			{
				$selectedRecipe = $recipe;
				$selectedRecipePositions = $this->Database->recipes_pos()->where('recipe_id', $recipe->id)->orderBy('ingredient_group');
				break;
			}
		}

		// Scale ingredients amount based on desired servings
		foreach ($selectedRecipePositions as $selectedRecipePosition)
		{
			$selectedRecipePosition->amount = $selectedRecipePosition->amount * ($selectedRecipe->desired_servings / $selectedRecipe->base_servings);
		}

		$selectedRecipeSubRecipes = $this->Database->recipes()->where('id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('name')->fetchAll();
		$selectedRecipeSubRecipesPositions = $this->Database->recipes_pos()->where('recipe_id IN (SELECT includes_recipe_id FROM recipes_nestings_resolved WHERE recipe_id = :1 AND includes_recipe_id != :1)', $selectedRecipe->id)->orderBy('ingredient_group')->fetchAll();

		// Scale ingredients amount based on desired servings
		foreach ($selectedRecipeSubRecipesPositions as $selectedSubRecipePosition)
		{
			$selectedSubRecipePosition->amount = $selectedSubRecipePosition->amount * ($selectedRecipe->desired_servings / $selectedRecipe->base_servings);
		}

		$includedRecipeIdsAbsolute = array();
		$includedRecipeIdsAbsolute[] = $selectedRecipe->id;
		foreach($selectedRecipeSubRecipes as $subRecipe)
		{
			$includedRecipeIdsAbsolute[] = $subRecipe->id;
		}

		return $this->AppContainer->view->render($response, 'recipes', [
			'recipes' => $recipes,
			'recipesFulfillment' => $this->RecipesService->GetRecipesFulfillment(),
			'recipesSumFulfillment' => $this->RecipesService->GetRecipesSumFulfillment(),
			'selectedRecipe' => $selectedRecipe,
			'selectedRecipePositions' => $selectedRecipePositions,
			'products' => $this->Database->products(),
			'quantityunits' => $this->Database->quantity_units(),
			'selectedRecipeSubRecipes' => $selectedRecipeSubRecipes,
			'selectedRecipeSubRecipesPositions' => $selectedRecipeSubRecipesPositions,
			'includedRecipeIdsAbsolute' => $includedRecipeIdsAbsolute
		]);
	}

	public function RecipeEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$recipeId = $args['recipeId'];
		if ($recipeId  == 'new')
		{
			$newRecipe = $this->Database->recipes()->createRow(array(
				'name' => $this->LocalizationService->Localize('New recipe')
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
			'recipesFulfillment' => $this->RecipesService->GetRecipesFulfillment(),
			'recipesSumFulfillment' => $this->RecipesService->GetRecipesSumFulfillment(),
			'recipes' =>  $this->Database->recipes()->orderBy('name'),
			'recipeNestings' =>  $this->Database->recipes_nestings()->where('recipe_id', $recipeId)
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
}
