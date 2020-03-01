<?php

namespace Grocy\Controllers;

class RecipesApiController extends BaseApiController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function AddNotFulfilledProductsToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

	public function ConsumeRecipe(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
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

	public function GetRecipeFulfillment(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			if(!isset($args['recipeId']))
			{
				return $this->ApiResponse($response, $this->getRecipesService()->GetRecipesResolved());
			}

			$recipeResolved = FindObjectInArrayByPropertyValue($this->getRecipesService()->GetRecipesResolved(), 'recipe_id', $args['recipeId']);
			if(!$recipeResolved)
			{
				throw new \Exception('Recipe does not exist');
			}
			else
			{
				return $this->ApiResponse($response, $recipeResolved);
			}
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
