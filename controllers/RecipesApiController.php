<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;

class RecipesApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->RecipesService = new RecipesService();
	}

	protected $RecipesService;

	public function AddNotFulfilledProductsToShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$this->RecipesService->AddNotFulfilledProductsToShoppingList($args['recipeId']);
		return $this->VoidApiActionResponse($response);
	}

	public function ConsumeRecipe(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->RecipesService->ConsumeRecipe($args['recipeId']);
			return $this->VoidApiActionResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->VoidApiActionResponse($response, false, 400, $ex->getMessage());
		}
	}
}
