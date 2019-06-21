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
	public function CreateSession($userId, $stayLoggedInPermanently = false)
	{
		$newSessionKey = $this->GenerateSessionKey();
		
		$expires = date('Y-m-d H:i:s', intval(time() + 2592000)); // Default is that sessions expire in 30 days
		if ($stayLoggedInPermanently === true)
		{
			$expires = date('Y-m-d H:i:s', PHP_INT_SIZE == 4 ? PHP_INT_MAX : PHP_INT_MAX>>32); // Never
		}

		$sessionRow = $this->Database->sessions()->createRow(array(
			'user_id' => $userId,
			'session_key' => $newSessionKey,
			'expires' => $expires
		));
		$sessionRow->save();

		return $newSessionKey;
	}

	public function RemoveSession($sessionKey)
	{
		$this->Database->sessions()->where('session_key', $sessionKey)->delete();
	}

	public function GetUserBySessionKey($sessionKey)
	{
		$sessionRow = $this->Database->sessions()->where('session_key', $sessionKey)->fetch();
		if ($sessionRow !== null)
		{
			return $this->Database->users($sessionRow->user_id);
		}
		return null;
	}

	public function GetDefaultUser()
	{
		return $this->Database->users(1);
	}

	private function GenerateSessionKey()
	{
		return RandomString(50);
	}
}
