<?php

use \Grocy\Helpers\BaseBarcodeLookupPlugin;

/*
	This class must extend BaseBarcodeLookupPlugin (in namespace \Grocy\Helpers)
*/
class DemoBarcodeLookupPlugin extends BaseBarcodeLookupPlugin
{
	/*
		To use this plugin, configure it in data/config.php like this:
		Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');
	*/

	/*
		To try it:
		Call the API function at /api/stock/external-barcode-lookup/{barcode}

		When you also add ?add=true as a query parameter to the API call,
		on a successful lookup the product is added to the database and in the output
		the new product id is included (automatically, nothing to do here in the plugin)
	*/

	/*
		Provided references:

		$this->Locations contains all locations
		$this->QuantityUnits contains all quantity units
	*/

	/*
		Useful hints:

		Get a quantity unit by name:
		$quantityUnit = FindObjectInArrayByPropertyValue($this->QuantityUnits, 'name', 'Piece');

		Get a location by name:
		$location = FindObjectInArrayByPropertyValue($this->Locations, 'name', 'Fridge');
	*/

	/*
		This class must implement the protected abstract function ExecuteLookup($barcode),
		which is called with the barcode that needs to be looked up and must return an
		associative array of the product model or null, when nothing was found for the barcode.

		The returned array must contain at least these properties:
		array(
			'name' => '',
			'location_id' => 1, // A valid id of a location object, check against $this->Locations
			'qu_id_purchase' => 1, // A valid id of quantity unit object, check against $this->QuantityUnits
			'qu_id_stock' => 1, // A valid id of quantity unit object, check against $this->QuantityUnits
			'qu_factor_purchase_to_stock' => 1, // Normally 1 when quantity unit stock and purchase is the same
			'barcode' => $barcode // The barcode of the product, maybe just pass through $barcode or manipulate it if necessary
		)
	*/
	protected function ExecuteLookup($barcode)
	{
		if ($barcode === 'x') // Demonstration when nothing is found
		{
			return null;
		}
		elseif ($barcode === 'e') // Demonstration when an error occurred
		{
			throw new \Exception('This is the error message from the plugin...');
		}
		else
		{
			return array(
				'name' => 'LookedUpProduct_' . RandomString(5),
				'location_id' => $this->Locations[0]->id,
				'qu_id_purchase' => $this->QuantityUnits[0]->id,
				'qu_id_stock' => $this->QuantityUnits[0]->id,
				'qu_factor_purchase_to_stock' => 1,
				'barcode' => $barcode
			);
		}
	}
}
