<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;

class RecipesApiController extends BaseApiController
{
	public function AddNotFulfilledProductsToShoppingList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_SHOPPINGLIST_ITEMS_ADD);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);
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
		User::checkPermission($request, User::PERMISSION_STOCK_CONSUME);

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
			if (!isset($args['recipeId']))
			{
				return $this->FilteredApiResponse($response, $this->getRecipesService()->GetRecipesResolved(), $request->getQueryParams());
			}

			$recipeResolved = FindObjectInArrayByPropertyValue($this->getRecipesService()->GetRecipesResolved(), 'recipe_id', $args['recipeId']);

			if (!$recipeResolved)
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

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
