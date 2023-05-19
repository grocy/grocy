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

	$datapath = 'data';

	if (getenv('GROCY_DATAPATH') !== false)
	{
		$datapath = getenv('GROCY_DATAPATH');
	}
	elseif (array_key_exists('GROCY_DATAPATH', $_SERVER))
	{
		$datapath = $_SERVER['GROCY_DATAPATH'];
	}

	if ($datapath[0] != '/')
	{
		$datapath = __DIR__ . '/../' . $datapath;
	}

	define('GROCY_DATAPATH', $datapath);
}

require_once __DIR__ . '/../helpers/PrerequisiteChecker.php';

try
{
	(new PrerequisiteChecker())->checkRequirements();
}
catch (ERequirementNotMet $ex)
{
	exit('Unable to run Grocy: ' . $ex->getMessage());
}

require_once __DIR__ . '/../app.php';
