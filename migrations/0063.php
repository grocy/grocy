<?php

// This is executed inside DatabaseMigrationService class/context

$localizationService = $this->getLocalizationService();
$db = $this->getDatabaseService()->GetDbConnection();

$defaultShoppingList = $db->shopping_lists()->where('id = 1')->fetch();
$defaultShoppingList->update([
	'name' => $localizationService->__t('Shopping list')
]);
