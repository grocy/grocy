<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Helpers\WebhookRunner;
use Grocy\Helpers\Grocycode;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RecipesApiController extends BaseApiController
{
	public function AddNotFulfilledProductsToShoppingList(Request $request, Response $response, array $args)
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

	public function ConsumeRecipe(Request $request, Response $response, array $args)
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

	public function GetRecipeFulfillment(Request $request, Response $response, array $args)
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

	public function CopyRecipe(Request $request, Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, [
				'created_object_id' => $this->getRecipesService()->CopyRecipe($args['recipeId'])
			]);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function RecipePrintLabel(Request $request, Response $response, array $args)
	{
		try
		{
			$recipe = $this->getDatabase()->recipes()->where('id', $args['recipeId'])->fetch();

			$webhookData = array_merge([
				'recipe' => $recipe->name,
				'grocycode' => (string)(new Grocycode(Grocycode::RECIPE, $args['recipeId'])),
			], GROCY_LABEL_PRINTER_PARAMS);

			if (GROCY_LABEL_PRINTER_RUN_SERVER)
			{
				(new WebhookRunner())->run(GROCY_LABEL_PRINTER_WEBHOOK, $webhookData, GROCY_LABEL_PRINTER_HOOK_JSON);
			}

			return $this->ApiResponse($response, $webhookData);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
