<?php

namespace Grocy\Services;

use LessQL\Result;

class RecipesService extends BaseService
{
	const RECIPE_TYPE_MEALPLAN_DAY = 'mealplan-day'; // A recipe per meal plan day => name = YYYY-MM-DD
	const RECIPE_TYPE_MEALPLAN_WEEK = 'mealplan-week'; // A recipe per meal plan week => name = YYYY-WW (week number)
	const RECIPE_TYPE_MEALPLAN_SHADOW = 'mealplan-shadow'; // A recipe per meal plan recipe (for separated stock fulfillment checking) => name = YYYY-MM-DD#<meal_plan.id>
	const RECIPE_TYPE_NORMAL = 'normal'; // Normal / manually created recipes

	public function AddNotFulfilledProductsToShoppingList($recipeId, $excludedProductIds = null)
	{
		$recipe = $this->getDataBase()->recipes($recipeId);
		$recipePositions = $this->GetRecipesPosResolved();

		if ($excludedProductIds == null)
		{
			$excludedProductIds = [];
		}

		foreach ($recipePositions as $recipePosition)
		{
			if ($recipePosition->recipe_id == $recipeId && !in_array($recipePosition->product_id, $excludedProductIds))
			{
				$product = $this->getDataBase()->products($recipePosition->product_id);
				$toOrderAmount = round(($recipePosition->missing_amount - $recipePosition->amount_on_shopping_list), 2);
				$quId = $product->qu_id_purchase;

				if ($recipe->not_check_shoppinglist == 1)
				{
					$toOrderAmount = round($recipePosition->missing_amount, 2);
				}

				// When the recipe ingredient option "Only check if any amount is in stock" is enabled,
				// any QU can be used and the amount is not based on qu_stock then
				// => Do the unit conversion here (if any)
				if ($recipePosition->only_check_single_unit_in_stock == 1)
				{
					$conversion = $this->getDatabase()->cache__quantity_unit_conversions_resolved()->where('product_id = :1 AND from_qu_id = :2 AND to_qu_id = :3', $recipePosition->product_id, $recipePosition->qu_id, $product->qu_id_stock)->fetch();
					if ($conversion != null)
					{
						$toOrderAmount = $toOrderAmount * $conversion->factor;
					}
					else
					{
						// No conversion exists => take the amount/unit as is
						$quId = $recipePosition->qu_id;
						$toOrderAmount = $recipePosition->missing_amount;
					}
				}

				if ($toOrderAmount > 0)
				{
					$alreadyExistingEntry = $this->getDatabase()->shopping_list()->where('product_id', $recipePosition->product_id)->fetch();
					if ($alreadyExistingEntry)
					{
						// Update
						$alreadyExistingEntry->update([
							'amount' => $alreadyExistingEntry->amount + $toOrderAmount
						]);
					}
					else
					{
						// Insert
						$shoppinglistRow = $this->getDataBase()->shopping_list()->createRow([
							'product_id' => $recipePosition->product_id,
							'amount' => $toOrderAmount,
							'qu_id' => $quId
						]);
						$shoppinglistRow->save();
					}
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

		$this->getDatabaseService()->GetDbConnectionRaw()->beginTransaction();
		try
		{
			foreach ($recipePositions as $recipePosition)
			{
				if ($recipePosition->only_check_single_unit_in_stock == 0 && $recipePosition->stock_amount > 0)
				{
					$amount = $recipePosition->recipe_amount;
					if ($recipePosition->stock_amount > 0 && $recipePosition->stock_amount < $recipePosition->recipe_amount)
					{
						$amount = $recipePosition->stock_amount;
					}

					$this->getStockService()->ConsumeProduct($recipePosition->product_id, $amount, false, StockService::TRANSACTION_TYPE_CONSUME, 'default', $recipeId, null, $transactionId, true, true);
				}
			}
		}
		catch (\Exception $ex)
		{
			$this->getDatabaseService()->GetDbConnectionRaw()->rollback();
			throw $ex;
		}
		$this->getDatabaseService()->GetDbConnectionRaw()->commit();

		$recipe = $this->getDatabase()->recipes()->where('id = :1', $recipeId)->fetch();
		$productId = $recipe->product_id;
		$amount = $recipe->desired_servings;
		if ($recipe->type == self::RECIPE_TYPE_MEALPLAN_SHADOW)
		{
			// Use "Produces product" of the original recipe
			$mealPlanEntry = $this->getDatabase()->meal_plan()->where('id = :1', explode('#', $recipe->name)[1])->fetch();
			$recipe = $this->getDatabase()->recipes()->where('id = :1', $mealPlanEntry->recipe_id)->fetch();
			$productId = $recipe->product_id;
			$amount = $mealPlanEntry->recipe_servings;
		}

		if (!empty($productId))
		{
			$product = $this->getDatabase()->products()->where('id = :1', $productId)->fetch();
			$recipeResolvedRow = $this->getDatabase()->recipes_resolved()->where('recipe_id = :1', $recipeId)->fetch();
			$this->getStockService()->AddProduct($productId, $amount, null, StockService::TRANSACTION_TYPE_SELF_PRODUCTION, date('Y-m-d'), $recipeResolvedRow->costs_per_serving, null, null, $dummyTransactionId, $product->default_stock_label_type, true, $recipe->name);
		}
	}

	public function GetRecipesPosResolved()
	{
		$sql = 'SELECT * FROM recipes_pos_resolved';
		return $this->getDataBaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetRecipesResolved($customWhere = null): Result
	{
		if ($customWhere == null)
		{
			return $this->getDatabase()->recipes_resolved();
		}
		else
		{
			return $this->getDatabase()->recipes_resolved()->where($customWhere);
		}
	}

	public function CopyRecipe($recipeId)
	{
		if (!$this->RecipeExists($recipeId))
		{
			throw new \Exception('Recipe does not exist');
		}

		$newName = $this->getLocalizationService()->__t('Copy of %s', $this->getDataBase()->recipes($recipeId)->name);

		$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO recipes (name, description, picture_file_name, base_servings, desired_servings, not_check_shoppinglist, type, product_id) SELECT :new_name, description, picture_file_name, base_servings, desired_servings, not_check_shoppinglist, type, product_id FROM recipes WHERE id = :recipe_id', ['recipe_id' => $recipeId, 'new_name' => $newName]);
		$lastInsertId = $this->getDatabase()->lastInsertId();
		$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO recipes_pos (recipe_id, product_id, amount, note, qu_id, only_check_single_unit_in_stock, ingredient_group, not_check_stock_fulfillment, variable_amount, price_factor) SELECT :last_insert_id, product_id, amount, note, qu_id, only_check_single_unit_in_stock, ingredient_group, not_check_stock_fulfillment, variable_amount, price_factor FROM recipes_pos WHERE recipe_id = :recipe_id', ['recipe_id' => $recipeId, 'last_insert_id' => $lastInsertId]);
		$this->getDatabaseService()->ExecuteDbStatement('INSERT INTO recipes_nestings (recipe_id, includes_recipe_id, servings) SELECT :last_insert_id, includes_recipe_id, servings FROM recipes_nestings WHERE recipe_id = :recipe_id', ['recipe_id' => $recipeId, 'last_insert_id' => $lastInsertId]);

		return $lastInsertId;
	}

	private function RecipeExists($recipeId)
	{
		$recipeRow = $this->getDataBase()->recipes()->where('id = :1', $recipeId)->fetch();
		return $recipeRow !== null;
	}
}
