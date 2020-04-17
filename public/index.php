<?php

// Definitions for embedded mode
if (file_exists(__DIR__ . '/../embedded.txt'))
{
	define('GROCY_IS_EMBEDDED_INSTALL', true);
	define('GROCY_DATAPATH', file_get_contents(__DIR__ . '/../embedded.txt'));
	define('GROCY_USER_ID', 1);
}
else
{
	define('GROCY_IS_EMBEDDED_INSTALL', false);
	define('GROCY_DATAPATH', __DIR__ . '/../data');
}

require_once __DIR__ . '/../helpers/PrerequisiteChecker.php';

try
{
	(new PrerequisiteChecker)->checkRequirements();
}
catch (ERequirementNotMet $ex)
{
    die('Unable to run grocy: ' . $ex->getMessage());
}

require_once __DIR__ . '/../app.php';
