<?php

namespace Grocy\Helpers;

abstract class BaseBarcodeLookupPlugin
{
	// That's a "self-referencing constant" and forces the child class to define it
	public const PLUGIN_NAME = self::PLUGIN_NAME;

	final public function __construct($locations, $quantityUnits, $userSettings)
	{
		$this->Locations = $locations;
		$this->QuantityUnits = $quantityUnits;
		$this->UserSettings = $userSettings;
	}

	protected $Locations;
	protected $QuantityUnits;
	protected $UserSettings;

	final public function Lookup($barcode)
	{
		$pluginOutput = $this->ExecuteLookup($barcode);

		if ($pluginOutput === null)
		{
			return $pluginOutput;
		}

		// Plugin must return an associative array
		if (!is_array($pluginOutput))
		{
			throw new \Exception('Plugin output must be an associative array');
		}

		if (!IsAssociativeArray($pluginOutput))
		{
			// $pluginOutput is at least an indexed array here
			throw new \Exception('Plugin output must be an associative array');
		}

		// Check for minimum needed properties
		$minimunNeededProperties = [
			'name',
			'location_id',
			'qu_id_purchase',
			'qu_id_stock',
			'__qu_factor_purchase_to_stock',
			'__barcode'
		];

		foreach ($minimunNeededProperties as $prop)
		{
			if (!array_key_exists($prop, $pluginOutput))
			{
				throw new \Exception("Plugin output does not provide needed property $prop");
			}
		}

		// $pluginOutput contains all needed properties here

		// Check if referenced entity ids are valid
		$locationId = $pluginOutput['location_id'];
		if (FindObjectInArrayByPropertyValue($this->Locations, 'id', $locationId) === null)
		{
			throw new \Exception("Provided location_id ($locationId) is not a valid location id");
		}

		$quIdPurchase = $pluginOutput['qu_id_purchase'];
		if (FindObjectInArrayByPropertyValue($this->QuantityUnits, 'id', $quIdPurchase) === null)
		{
			throw new \Exception("Provided qu_id_purchase ($quIdPurchase) is not a valid quantity unit id");
		}

		$quIdStock = $pluginOutput['qu_id_stock'];
		if (FindObjectInArrayByPropertyValue($this->QuantityUnits, 'id', $quIdStock) === null)
		{
			throw new \Exception("Provided qu_id_stock ($quIdStock) is not a valid quantity unit id");
		}

		$quFactor = $pluginOutput['__qu_factor_purchase_to_stock'];
		if (empty($quFactor) || !is_numeric($quFactor))
		{
			throw new \Exception('Provided __qu_factor_purchase_to_stock is empty or not a number');
		}

		return $pluginOutput;
	}

	abstract protected function ExecuteLookup($barcode);
}
