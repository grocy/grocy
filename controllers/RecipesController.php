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
		return $this->AppContainer->view->render($response, 'recipes', [
			'recipes' => $this->Database->recipes()->orderBy('name'),
			'recipesFulfillment' => $this->RecipesService->GetRecipesFulfillment(),
			'recipesSumFulfillment' => $this->RecipesService->GetRecipesSumFulfillment()
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
			'products' => $this->Database->products()->orderBy('name'),
			'quantityunits' => $this->Database->quantity_units()->orderBy('name'),
			'recipesFulfillment' => $this->RecipesService->GetRecipesFulfillment(),
			'recipesSumFulfillment' => $this->RecipesService->GetRecipesSumFulfillment()
		]);
	}

	public function RecipePosEditForm(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		if ($args['recipePosId'] == 'new')
		{
			return $this->AppContainer->view->render($response, 'recipeposform', [
				'mode' => 'create',
				'recipe' =>  $this->Database->recipes($args['recipeId']),
				'products' =>  $this->Database->products()
			]);
		}
		else
		{
			return $this->AppContainer->view->render($response, 'recipeposform', [
				'mode' => 'edit',
				'recipe' =>  $this->Database->recipes($args['recipeId']),
				'recipePos' =>  $this->Database->recipes_pos($args['recipePosId']),
				'products' =>  $this->Database->products()
			]);
		}
	}
}
