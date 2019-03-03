<?php

namespace Grocy\Services;

use \Grocy\Services\StockService;

class RecipesService extends BaseService
{
	public function __construct()
	{
		parent::__construct();
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function GetRecipesFulfillment()
	{
		$sql = 'SELECT * from recipes_fulfillment';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetRecipesSumFulfillment()
	{
		$sql = 'SELECT * from recipes_fulfillment_sum';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function AddNotFulfilledProductsToShoppingList($recipeId, $excludedProductIds = null)
	{
		$recipe = $this->Database->recipes($recipeId);

		$recipePositions = $this->GetRecipesFulfillment();
		foreach ($recipePositions as $recipePosition)
		{
			if($recipePosition->recipe_id == $recipeId && !in_array($recipePosition->product_id, $excludedProductIds))
			{
				$product = $this->Database->products($recipePosition->product_id);
				
				$toOrderAmount = ceil(($recipePosition->missing_amount - $recipePosition->amount_on_shopping_list) / $product->qu_factor_purchase_to_stock);
				if ($recipe->not_check_shoppinglist == 1)
				{
					$toOrderAmount = ceil($recipePosition->missing_amount / $product->qu_factor_purchase_to_stock);
				}
				
				if($toOrderAmount > 0)
				{
					$shoppinglistRow = $this->Database->shopping_list()->createRow(array(
						'product_id' => $recipePosition->product_id,
						'amount' => $toOrderAmount,
						'note' => $this->LocalizationService->Localize('Added for recipe #1', $recipe->name)
					));
					$shoppinglistRow->save();
				}
			}
		}
	}

	public function ConsumeRecipe($recipeId)
	{
		if (!$this->RecipeExists($recipeId))
		{
			throw new \Exception('Recipe does not exist');
		}

		$recipePositions = $this->Database->recipes_pos()->where('recipe_id', $recipeId)->fetchAll();
		foreach ($recipePositions as $recipePosition)
		{
			if ($recipePosition->only_check_single_unit_in_stock == 0)
			{
				$this->StockService->ConsumeProduct($recipePosition->product_id, $recipePosition->amount, false, StockService::TRANSACTION_TYPE_CONSUME, 'default', $recipeId);
			}
		}
	}

	private function RecipeExists($recipeId)
	{
		$recipeRow = $this->Database->recipes()->where('id = :1', $recipeId)->fetch();
		return $recipeRow !== null;
	}
}
