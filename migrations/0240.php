<?php

// This is executed inside DatabaseMigrationService class/context

// This is now a built-in plugin
$filePath = GROCY_DATAPATH . '/plugins/DemoBarcodeLookupPlugin.php';
if (file_exists($filePath))
{
	unlink($filePath);
}
