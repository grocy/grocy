<?php

namespace Grocy\Services;

use LessQL\Result;

class RecipesService extends BaseService
{
	const RECIPE_TYPE_MEALPLAN_DAY = 'mealplan-day';

	const RECIPE_TYPE_MEALPLAN_WEEK = 'mealplan-week';

	const RECIPE_TYPE_NORMAL = 'normal';

	public function AddNotFulfilledProductsToShoppingList($recipeId, $excludedProductIds = null)
	{
		$recipe = $this->getDataBase()->recipes($recipeId);

		$recipePositions = $this->GetRecipesPosResolved();

		foreach ($recipePositions as $recipePosition)
		{
			if ($recipePosition->recipe_id == $recipeId && !in_array($recipePosition->product_id, $excludedProductIds))
			{
				$product = $this->getDataBase()->products($recipePosition->product_id);

				$toOrderAmount = round(($recipePosition->missing_amount - $recipePosition->amount_on_shopping_list), 2);

				if ($recipe->not_check_shoppinglist == 1)
				{
					$toOrderAmount = round($recipePosition->missing_amount, 2);
				}

				if ($toOrderAmount > 0)
				{
					$shoppinglistRow = $this->getDataBase()->shopping_list()->createRow([
						'product_id' => $recipePosition->product_id,
						'amount' => $toOrderAmount,
						'note' => $this->getLocalizationService()->__t('Added for recipe %s', $recipe->name)
					]);
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

		$transactionId = uniqid();
		$recipePositions = $this->getDatabase()->recipes_pos_resolved()->where('recipe_id', $recipeId)->fetchAll();

		foreach ($recipePositions as $recipePosition)
		{
			if ($recipePosition->only_check_single_unit_in_stock == 0)
			{
				$this->getStockService()->ConsumeProduct($recipePosition->product_id, $recipePosition->recipe_amount, false, StockService::TRANSACTION_TYPE_CONSUME, 'default', $recipeId, null, $transactionId, true, true);
			}
		}

		$recipeRow = $this->getDatabase()->recipes()->where('id = :1', $recipeId)->fetch();
		if (!empty($recipeRow->product_id))
		{
			$recipeResolvedRow = $this->getDatabase()->recipes_resolved()->where('recipe_id = :1', $recipeId)->fetch();
			$this->getStockService()->AddProduct($recipeRow->product_id, floatval($recipeRow->desired_servings), null, StockService::TRANSACTION_TYPE_SELF_PRODUCTION, date('Y-m-d'), floatval($recipeResolvedRow->costs));
		}
	}

	public function GetRecipesPosResolved()
	{
		$sql = 'SELECT * FROM recipes_pos_resolved';
		return $this->getDataBaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetRecipesResolved(): Result
	{
		return $this->getDatabase()->recipes_resolved();
	}

	public function __construct()
	{
		parent::__construct();
	}

	private function RecipeExists($recipeId)
	{
		$recipeRow = $this->getDataBase()->recipes()->where('id = :1', $recipeId)->fetch();
		return $recipeRow !== null;
	}
}
