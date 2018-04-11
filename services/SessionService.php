<?php

namespace Grocy\Services;

class SessionService extends BaseService
{
	/**
	 * @return boolean
	 */
	public function IsValidSession($sessionKey)
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
	public function CreateSession()
	{
		if (!file_exists(__DIR__ . '/../data/sessions'))
		{
			mkdir(__DIR__ . '/../data/sessions');
		}

		$now = time();
		foreach (new \FilesystemIterator(__DIR__ . '/../data/sessions') as $file)
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

	public function RemoveSession($sessionKey)
	{
		unlink(__DIR__ . "/../data/sessions/$sessionKey.txt");
	}
}
