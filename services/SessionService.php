<?php

class SessionService
{
	/**
	 * @return boolean
	 */
	public static function IsValidSession($sessionKey)
	{
		if ($sessionKey === null || empty($sessionKey))
		{
			return false;
		}
		else
		{
			return file_exists(__DIR__ . "/../data/sessions/$sessionKey.txt");
		}
	}

	/**
	 * @return string
	 */
	public static function CreateSession()
	{
		if (!file_exists(__DIR__ . '/../data/sessions'))
		{
			mkdir(__DIR__ . '/../data/sessions');
		}

		$now = time();
		foreach (new FilesystemIterator(__DIR__ . '/../data/sessions') as $file)
		{
			if ($now - $file->getCTime() >= 2678400) //31 days
			{
				unlink(__DIR__ . '/../data/sessions/' . $file->getFilename());
			}
		}

		$newSessionKey = uniqid() . uniqid() . uniqid();
		file_put_contents(__DIR__ . "/../data/sessions/$newSessionKey.txt", '');
		return $newSessionKey;
	}

	public static function RemoveSession($sessionKey)
	{
		unlink(__DIR__ . "/../data/sessions/$sessionKey.txt");
	}
}
