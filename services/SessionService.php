<?php

namespace Grocy\Services;

class SessionService extends BaseService
{
	const SESSION_COOKIE_NAME = 'grocy_session';

	public function CreateSession($userId, $stayLoggedInPermanently = false)
	{
		$newSessionKey = $this->GenerateSessionKey();
		$expires = date('Y-m-d H:i:s', time() + 2592000);

		// Default is that sessions expire in 30 days
		if ($stayLoggedInPermanently === true)
		{
			$expires = date('Y-m-d H:i:s', PHP_INT_SIZE == 4 ? PHP_INT_MAX : PHP_INT_MAX >> 32); // Never
		}

		$sessionRow = $this->getDatabase()->sessions()->createRow([
			'user_id' => $userId,
			'session_key' => $newSessionKey,
			'expires' => $expires
		]);
		$sessionRow->save();

		return $newSessionKey;
	}

	public function GetDefaultUser()
	{
		return $this->getDatabase()->users()->orderBy('id')->limit(1)->fetch();
	}

	public function GetUserBySessionKey($sessionKey)
	{
		$sessionRow = $this->getDatabase()->sessions()->where('session_key', $sessionKey)->fetch();
		if ($sessionRow !== null)
		{
			return $this->getDatabase()->users($sessionRow->user_id);
		}

		return null;
	}

	public function IsValidSession($sessionKey)
	{
		if ($sessionKey === null || empty($sessionKey))
		{
			return false;
		}
		else
		{
			$sessionRow = $this->getDatabase()->sessions()->where('session_key = :1 AND expires > :2', $sessionKey, date('Y-m-d H:i:s', time()))->fetch();
			if ($sessionRow !== null)
			{
				// This should not change the database file modification time as this is used
				// to determine if REALLY something has changed
				$dbModTime = $this->getDatabaseService()->GetDbChangedTime();
				$sessionRow->update([
					'last_used' => date('Y-m-d H:i:s', time())
				]);
				$this->getDatabaseService()->SetDbChangedTime($dbModTime);

				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public function RemoveSession($sessionKey)
	{
		$this->getDatabase()->sessions()->where('session_key', $sessionKey)->delete();
	}

	private function GenerateSessionKey()
	{
		return RandomString(50);
	}
}
