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
			return $this->Database->sessions()->where('session_key = :1 AND expires > :2', $sessionKey, time())->count() === 1;
		}
	}

	/**
	 * @return string
	 */
	public function CreateSession()
	{
		$newSessionKey = uniqid() . uniqid() . uniqid();
		
		$sessionRow = $this->Database->sessions()->createRow(array(
			'session_key' => $newSessionKey,
			'expires' => time() + 2592000 //30 days
		));
		$sessionRow->save();

		return $newSessionKey;
	}

	public function RemoveSession($sessionKey)
	{
		$this->Database->sessions()->where('session_key', $sessionKey)->delete();
	}
}
