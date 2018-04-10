<?php

class ApplicationService
{
	/**
	 * @return boolean
	 */
	public static function IsDemoInstallation()
	{
		return file_exists(__DIR__ . '/../data/demo.txt');
	}

	private static $InstalledVersion;
	/**
	 * @return string
	 */
	public static function GetInstalledVersion()
	{
		if (self::$InstalledVersion == null)
		{
			self::$InstalledVersion = preg_replace("/\r|\n/", '', file_get_contents(__DIR__ . '/../version.txt'));
		}

		return self::$InstalledVersion;
	}
}
