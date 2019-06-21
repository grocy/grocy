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
		$requestBody = $request->getParsedBody();
		$excludedProductIds = null;

		if ($requestBody !== null && array_key_exists('excludedProductIds', $requestBody))
		{
			$excludedProductIds = $requestBody['excludedProductIds'];
		}
		
		$this->RecipesService->AddNotFulfilledProductsToShoppingList($args['recipeId'], $excludedProductIds);
		return $this->EmptyApiResponse($response);
	}

	public function ConsumeRecipe(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$this->RecipesService->ConsumeRecipe($args['recipeId']);
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

  public function GetRecipeRequirements(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
  {
    try { 
      if(!$args['recipeId']){
        return $this->ApiResponse($this->RecipesService->GetRecipesResolved());
      }
      $recipeResolved = FindObjectInArrayByPropertyValue($this->RecipesService->GetRecipesResolved(), 'recipe_id', $args['recipeId']);
      if(!$recipeResolved) {
        $errorMsg ='Recipe requirments do not exist for recipe_id ' . $args['recipe_id'];
        $GenericError = $this->GenericErrorResponse($response, $errorMsg);
        return $GenericError;
      }
      return $this->ApiResponse($recipeResolved);
    } 
    catch (\Exception $ex)
    {
			return $this->GenericErrorResponse($response, $ex->getMessage());
    }
  }
}
