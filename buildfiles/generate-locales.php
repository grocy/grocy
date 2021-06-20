<?php
use Grocy\Services\LocalizationService;

/* This file statically generates json to handle
 * frontend translations.
 */
define("GROCY_DATAPATH", __DIR__ . '/../data');

// Load composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';
// Load config files
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/../config-dist.php'; // For not in own config defined values we use the default ones

echo "Searching for localizations in " . __DIR__ . '/../localization/* \n';

$translations = array_filter(glob(__DIR__ . '/../localization/*'), 'is_dir');

// ensure the target directory is there
if(!is_dir(__DIR__ . '/../public/js/locales/grocy/')) {
	mkdir(__DIR__ . '/../public/js/locales/grocy/', 0777, true);
}

foreach($translations as $lang) {
	$culture = basename($lang);
	echo "Generating " . $culture . "...\n";
	$ls = LocalizationService::getInstance($culture, true);
	$ls->LoadLocalizations(false);
	file_put_contents(__DIR__ .'/../public/js/locales/grocy/'.$culture.'.json', $ls->GetPoAsJsonString());
}