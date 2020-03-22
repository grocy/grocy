<?php

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
