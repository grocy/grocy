<?php

require_once __DIR__ . '/../helpers/PrerequisiteChecker.php';

try {
	(new PrerequisiteChecker)->checkRequirements();
} catch (ERequirementNotMet $e) {
    die("Unable to run grocy: " . $e->getMessage());
}

require_once __DIR__ . '/../app.php';

?>