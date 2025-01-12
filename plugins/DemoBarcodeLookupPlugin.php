<?php

use Grocy\Helpers\BaseBarcodeLookupPlugin;

/*
	This class must extend BaseBarcodeLookupPlugin (in namespace Grocy\Helpers)
*/
class DemoBarcodeLookupPlugin extends BaseBarcodeLookupPlugin
{
	/*
		To use this plugin, configure it in data/config.php like this:
		Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');
	*/

	/*
		To try it:

		Call the API function at /api/stock/barcodes/external-lookup/{barcode}

		Or use the product picker workflow "External barcode lookup"

		When you also add ?add=true as a query parameter to the API call,
		on a successful lookup the product is added to the database and in the output
		the new product id is included (automatically, nothing to do here in the plugin)
	*/

	/*
		Provided references:

		$this->Locations contains all locations
		$this->QuantityUnits contains all quantity units
		$this->UserSettings contains all user settings
	*/

	/*
		Useful hints:

		Get a quantity unit by name:
		$quantityUnit = FindObjectInArrayByPropertyValue($this->QuantityUnits, 'name', 'Piece');

		Get a location by name:
		$location = FindObjectInArrayByPropertyValue($this->Locations, 'name', 'Fridge');
	*/

	// Provide a name
	public const PLUGIN_NAME = 'Demo';

	/*
		This class must implement the protected abstract function ExecuteLookup($barcode),
		which is called with the barcode that needs to be looked up and must return an
		associative array of the product model (see the "products" database table for all available properties/columns)
		or null when nothing was found for the barcode:
		[
			// Required properties:
			'name' => '',
			'location_id' => 1, // A valid id of a location object, check against $this->Locations
			'qu_id_purchase' => 1, // A valid id of a quantity unit object, check against $this->QuantityUnits
			'qu_id_stock' => 1, // A valid id of a quantity unit object, check against $this->QuantityUnits

			// Required virtual properties (not part of the product object, will be automatically handled as needed):
			'__qu_factor_purchase_to_stock' => 1, // Normally 1 when quantity unit stock and purchase is the same
			'__barcode' => $barcode // The barcode of the product, maybe just pass through $barcode or manipulate it if necessary

			// Optional virtual properties (not part of the product object, will be automatically handled as needed):
			'__image_url' => '' // When provided, the corresponding image will be downloaded and set as the product picture
		]
	*/
	protected function ExecuteLookup($barcode)
	{
		if ($barcode === 'nothing')
		{
			// Demonstration when nothing is found
			return null;
		}
		elseif ($barcode === 'error')
		{
			// Demonstration when an error occurred
			throw new \Exception('This is the error message from the plugin...');
		}
		else
		{
			// Take the preset user setting or otherwise simply the first existing location
			$locationId = $this->Locations[0]->id;
			if ($this->UserSettings['product_presets_location_id'] != -1)
			{
				$locationId = $this->UserSettings['product_presets_location_id'];
			}

			// Take the preset user setting or otherwise simply the first existing quantity unit
			$quId = $this->QuantityUnits[0]->id;
			if ($this->UserSettings['product_presets_qu_id'] != -1)
			{
				$quId = $this->UserSettings['product_presets_qu_id'];
			}

			return [
				'name' => 'LookedUpProduct_' . RandomString(5),
				'location_id' => $locationId,
				'qu_id_purchase' => $quId,
				'qu_id_stock' => $quId,
				'__qu_factor_purchase_to_stock' => 1,
				'__barcode' => $barcode
			];
		}
	}
}
