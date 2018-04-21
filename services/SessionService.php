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
			$sessionRow = $this->Database->sessions()->where('session_key = :1 AND expires > :2', $sessionKey, date('Y-m-d H:i:s', time()))->fetch();
			if ($sessionRow !== null)
			{
				$sessionRow->update(array(
					'last_used' => date('Y-m-d H:i:s', time())
				));
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * @return string
	 */
	public function CreateSession()
	{
		$newSessionKey = $this->GenerateSessionKey();
		
		$sessionRow = $this->Database->sessions()->createRow(array(
			'session_key' => $newSessionKey,
			'expires' => date('Y-m-d H:i:s', time() + 2592000) // Default is that sessions expire in 30 days
		));
		$sessionRow->save();

		return $newSessionKey;
	}

	public function RemoveSession($sessionKey)
	{
		$this->Database->sessions()->where('session_key', $sessionKey)->delete();
	}

	private function GenerateSessionKey()
	{
		return RandomString(50);
	}
}
