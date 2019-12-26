<?php

// This is executed inside DatabaseMigrationService class/context

use \Grocy\Services\LocalizationService;
$localizationService = $this->getLocalizationService();

$db = $this->getDatabaseService()->GetDbConnection();

$defaultShoppingList = $db->shopping_lists()->where('id = 1')->fetch();
$defaultShoppingList->update(array(
	'name' => $localizationService->__t('Shopping list')
));
