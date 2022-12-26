<?php

// This is executed inside DatabaseMigrationService class/context

$db = $this->getDatabaseService()->GetDbConnection();

// Reset the password of the user "admin" to "admin"
$adminUserRow = $db->users()->where('username', 'admin')->fetch();

if ($adminUserRow == null)
{
	$adminUserRow = $db->users()->createRow([
		'username' => 'admin'
	]);
}

$adminUserRow->update([
	'password' => password_hash('admin', PASSWORD_DEFAULT)
]);
