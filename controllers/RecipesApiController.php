<?php

namespace Grocy\Controllers;

use \Grocy\Services\RecipesService;

class RecipesApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $RecipesService = null;

	protected function getRecipesService()
	{
		if($this->RecipesService == null)
		{
			$this->RecipesService = RecipesService::getInstance();
		}
		return $this->RecipesService;
	}

	public function AddNotFulfilledProductsToShoppingList(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$requestBody = $request->getParsedBody();
		$excludedProductIds = null;

		if ($requestBody !== null && array_key_exists('excludedProductIds', $requestBody))
		{
			$excludedProductIds = $requestBody['excludedProductIds'];
		}

		$this->getRecipesService()->AddNotFulfilledProductsToShoppingList($args['recipeId'], $excludedProductIds);
		return $this->EmptyApiResponse($response);
	}

	public function ConsumeRecipe(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->getRecipesService()->ConsumeRecipe($args['recipeId']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function GetRecipeFulfillment(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			if(!isset($args['recipeId']))
			{
				return $this->ApiResponse($this->getRecipesService()->GetRecipesResolved());
			}

			$recipeResolved = FindObjectInArrayByPropertyValue($this->getRecipesService()->GetRecipesResolved(), 'recipe_id', $args['recipeId']);
			if(!$recipeResolved)
			{
				throw new \Exception('Recipe does not exist');
			}
			else
			{
				return $this->ApiResponse($recipeResolved);
			}
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
