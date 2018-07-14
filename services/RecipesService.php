<?php

namespace Grocy\Services;

class RecipesService extends BaseService
{
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

	public function AddNotFulfilledProductsToShoppingList($recipeId)
	{
		$recipe = $this->Database->recipes($recipeId);

		$recipePositions = $this->GetRecipesFulfillment();
		foreach ($recipePositions as $recipePosition)
		{
			if($recipePosition->recipe_id == $recipeId)
			{
				$toOrderAmount = $recipePosition->missing_amount - $recipePosition->amount_on_shopping_list;
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
}
