<?php

namespace Grocy\Helpers;

class KrogerToGrocyConverter
{
	// Fields returned: 'name', 'location_id', 'qu_id_purchase', 'qu_id_stock',
	// 'qu_factor_purchase_to_stock', 'barcode', 'default_best_before_days'
	// 'quantity', 'transaction_date', 'price_paid', 'picture_url', 'min_stock_amount'
	public static function ConvertJson($data, $default_quantity_units, $default_location_id) 
	{
		if ($data != null && array_key_exists("data", $data))
		{
			$data = $data['data'];
		}
		
		$products = array();
		foreach ($data as &$receipt) 
		{
			if (!array_key_exists("items", $receipt))
			{
				continue;
			}

			$dateArray = date_parse($receipt['transactionTime']) ?: getdate();
			$transactionDate = sprintf("%04d-%02d-%02d", $dateArray["year"], $dateArray["month"], $dateArray["day"]);
			foreach ($receipt['items'] as &$item)
			{
				if (!array_key_exists("detail", $item))
				{
					continue;
				}

				if (array_key_exists("itemType", $item) && $item['itemType'] == 'MISC_TRANS_RECEIPT')
				{
					continue;
				}

				$barcode = KrogerToGrocyConverter::ConvertUpcToBarcode($item["baseUpc"]);

				$min_stock_amount = 1;
				$quantity = $item['quantity'];
				if ($item['weighted'] && array_key_exists('averageWeight', $item['detail']))
				{
					$quantity = round($item['quantity'] / $item['detail']['averageWeight']);
				}
				else if ($item['weighted'])
				{
					// this is semi-arbitrary; some things are bought by weight but with no way to determine quantity
					$min_stock_amount = $item['quantity'] / 4;
				}

				$quantity_units = $default_quantity_units;
				if (array_key_exists('unitOfMeasure', $item))
				{
					if ($item['unitOfMeasure'] == 'EA')
					{
						$quantity_units = 3 /*pack*/;
					}
					else if ($item['unitOfMeasure'] == 'LBR')
					{
						$quantity_units = 2 /*piece*/;
					}
				}

				$best_before_days = 21;
				$location = $default_location_id;
				if (array_key_exists('categories', $item['detail']) && count($item['detail']['categories']) > 0)
				{
					$category = $item['detail']['categories'][0]['categoryCode'];
					switch (intval($category))
					{
						case 1 /*adult beverage*/:
							$location = 3 /*pantry*/;
							$quantity_units = 10 /*bottle*/;
							$best_before_days = -1;
							break;
						case 7 /*baking goods*/:
						case 10 /*breakfast*/:
						case 12 /*canned and packaged*/:
						case 13 /*cleaning products*/:
						case 33 /*pasta, sauces, & grain*/:
						case 73 /*natural & organic*/:
						case 75 /*personal care*/:
						case 76 /*health*/:
							$location = 3 /*pantry*/;
							$best_before_days = -1;
							break;
						case 36 /*produce*/:
						case 6 /*bakery*/:
							$location = 3 /*pantry*/;
							break;
						case 15 /*dairy*/:
						case 16 /*deli*/:
						case 28 /*meat & seafood*/:
							$location = 2 /*fridge*/;
							break;
						case 20 /*frozen*/:
							$location = 6 /*freezer*/;
							$best_before_days = -1;
							break;
						case 37 /*snacks*/:
						case 77 /*candy*/:
							$location = 4 /*candy cupboard*/;
							$best_before_days = 360;
							break;
					}
				}

				$product = array(
					'name' => $item["detail"]["description"],
					'location_id' => $location,
					'qu_id_purchase' => $quantity_units,
					'qu_id_stock' => $quantity_units,
					'qu_factor_purchase_to_stock' => 1,
					'barcode' => $barcode,
					'default_best_before_days' => $best_before_days,
					'quantity' => $quantity,
					'min_stock_amount' => $min_stock_amount,
					'transaction_date' => $transactionDate,
					'price_paid' => $item['pricePaid'] / $item['quantity'],
					'picture_url' => (array_key_exists("mainImage", $item['detail']) ? $item['detail']['mainImage'] : null)
				);

				array_push($products, $product);
			}
		}
		
		return $products;
	}
	
	// Anatomy of a barcode: 0		07222526014		 2
	//						 ^			 ^			 ^
	//				   country code		upc	 computed check digit
	public static function ConvertUpcToBarcode($upc)
	{
		$upclen = strlen($upc);
		if ($upclen < 11 && intval($upc) > 0) 
		{
			throw new \Exception('UPC code should be at least 11 digits');
		}

		$countryCode = $upclen > 11 ? $upc[$upclen - 12] : 0 /*US/Canda*/;
		$upc = substr($upc, $upclen - 11);

		$checkSum = (intval($upc[0]) + intval($upc[2]) + intval($upc[4]) + intval($upc[6]) + intval($upc[8]) + $upc[10]) * 3;
		$checkSum = $checkSum + intval($upc[1]) + intval($upc[3]) + intval($upc[5]) + intval($upc[7]) + intval($upc[9]);
		
		$checkDigit = (10 - ($checkSum % 10)) % 10;
		
		return $countryCode . $upc . strval($checkDigit);
	}
}

?>