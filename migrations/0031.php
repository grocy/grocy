<?php

// This is executed inside DatabaseMigrationService class/context

$localizationService = $this->getLocalizationService();
$db = $this->getDatabaseService()->GetDbConnection();

if ($db->quantity_units()->count() === 0)
{
	// Create 2 default quantity units
	$newRow = $db->quantity_units()->createRow([
		'name' => $localizationService->__n(1, 'Piece', 'Pieces'),
		'name_plural' => $localizationService->__n(2, 'Piece', 'Pieces')
	]);
	$newRow->save();
	$newRow = $db->quantity_units()->createRow([
		'name' => $localizationService->__n(1, 'Pack', 'Packs'),
		'name_plural' => $localizationService->__n(2, 'Pack', 'Packs')
	]);
	$newRow->save();
}

if ($db->locations()->count() === 0)
{
	// Create a default location
	$newRow = $db->locations()->createRow([
		'name' => $localizationService->__t('Fridge')
	]);
	$newRow->save();
}
