<?php

// This is executed inside DatabaseMigrationService class/context

use \Grocy\Services\LocalizationService;
$localizationService = new LocalizationService(GROCY_CULTURE);

$db = $this->DatabaseService->GetDbConnection();

$defaultShoppingList = $this->Database->shopping_lists()->where('id = 1')->fetch();
$defaultShoppingList->update(array(
	'name' => $localizationService->__t('Shopping list')
));
