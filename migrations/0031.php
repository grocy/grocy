<?php

// This is executed inside DatabaseMigrationService class/context

use \Grocy\Services\LocalizationService;
$localizationService = new LocalizationService(GROCY_CULTURE);

$db = $this->DatabaseService->GetDbConnection();

if ($db->quantity_units()->count() === 0)
{
	// Create 2 default quantity units
	$newRow = $db->quantity_units()->createRow(array(
		'name' => $localizationService->Localize('Piece'),
		'name_plural' => $localizationService->Localize('Pieces')
	));
	$newRow->save();
	$newRow = $db->quantity_units()->createRow(array(
		'name' => $localizationService->Localize('Pack'),
		'name_plural' => $localizationService->Localize('Packs')
	));
	$newRow->save();
}

if ($db->locations()->count() === 0)
{
	// Create a default location
	$newRow = $db->locations()->createRow(array(
		'name' => $localizationService->Localize('Fridge')
	));
	$newRow->save();
}
