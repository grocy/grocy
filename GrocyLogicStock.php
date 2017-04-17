<?php

class GrocyLogicStock
{
	public static function GetCurrentStock()
	{
		$db = Grocy::GetDbConnectionRaw();
		return $db->query('SELECT product_id, SUM(amount) AS amount, MIN(best_before_date) AS best_before_date from stock GROUP BY product_id ORDER BY MIN(best_before_date) ASC')->fetchAll(PDO::FETCH_OBJ);
	}

	public static function GetProductDetails(int $productId)
	{
		$db = Grocy::GetDbConnection();

		$product = $db->products($productId);
		$productStockAmount = $db->stock()->where('product_id', $productId)->sum('amount');
		$productLastPurchased = $db->stock()->where('product_id', $productId)->max('purchased_date');
		$productLastUsed = $db->consumptions()->where('product_id', $productId)->max('used_date');
		$quPurchase = $db->quantity_units($product->qu_id_purchase);
		$quStock = $db->quantity_units($product->qu_id_stock);

		return array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $productStockAmount,
			'quantity_unit_purchase' => $quPurchase,
			'quantity_unit_stock' => $quStock
		);
	}

	public static function ConsumeProduct(int $productId, int $amount, bool $spoiled)
	{
		$db = Grocy::GetDbConnection();

		$productStockAmount = $db->stock()->where('product_id', $productId)->sum('amount');
		$potentialStockEntries = $db->stock()->where('product_id', $productId)->orderBy('purchased_date', 'ASC')->fetchAll(); //FIFO

		if ($amount > $productStockAmount)
		{
			return false;
		}

		foreach ($potentialStockEntries as $stockEntry)
		{
			if ($amount == 0)
			{
				break;
			}

			if ($amount >= $stockEntry->amount) //Take the whole stock entry
			{
				$consumptionRow = $db->consumptions()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $stockEntry->amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'spoiled' => $spoiled,
					'stock_id' => $stockEntry->stock_id
				));
				$consumptionRow->save();

				$amount -= $stockEntry->amount;
				$stockEntry->delete();
			}
			else //Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
			{
				$consumptionRow = $db->consumptions()->createRow(array(
					'product_id' => $stockEntry->product_id,
					'amount' => $amount,
					'best_before_date' => $stockEntry->best_before_date,
					'purchased_date' => $stockEntry->purchased_date,
					'spoiled' => $spoiled,
					'stock_id' => $stockEntry->stock_id
				));
				$consumptionRow->save();

				$restStockAmount = $stockEntry->amount - $amount;
				$amount = 0;

				$stockEntry->update(array(
					'amount' => $restStockAmount
				));
			}
		}

		return true;
	}
}
