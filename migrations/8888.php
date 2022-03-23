<?php

// This migration is always executed (on every migration run, not only once)

// This is executed inside DatabaseMigrationService class/context

// When FEATURE_FLAG_STOCK_LOCATION_TRACKING is disabled,
// some places assume that there exists a location with id 1,
// so make sure that this location is available in that case
if (!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
{
	$db = $this->getDatabaseService()->GetDbConnection();

	if ($db->locations()->where('id', 1)->count() === 0)
	{
		$defaultLocation = $db->locations()->createRow([
			'id' => 1,
			'name' => 'Default'
		]);
		$defaultLocation->save();
	}
}
