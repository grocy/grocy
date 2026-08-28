<?php

namespace Grocy\Services;

class SessionService extends BaseService
{
	const int SESSION_TOKEN_TYPE_ACCESS = 1;
	const int SESSION_TOKEN_TYPE_REMEMBER_ME = 2;
	const string SESSION_TOKEN_COOKIE_NAME_ACCESS = 'grocy_session_access_token';
	const string SESSION_TOKEN_COOKIE_NAME_REMEMBER_ME = 'grocy_session_remember_me_token';
	const int SESSION_TOKEN_EXPIRATION_SECONDS_ACCESS = 864000; // 10 Days
	const int SESSION_TOKEN_EXPIRATION_SECONDS_REMEMBER_ME = 15552000; // 180 Days

	public function CreateToken(int $tokenType, int $userId, string $clientInfo)
	{
		$newToken = bin2hex(random_bytes(32));

		$expires = date('Y-m-d H:i:s');
		switch ($tokenType)
		{
			case self::SESSION_TOKEN_TYPE_ACCESS:
				$expires = date('Y-m-d H:i:s', time() + self::SESSION_TOKEN_EXPIRATION_SECONDS_ACCESS);
				break;

			case self::SESSION_TOKEN_TYPE_REMEMBER_ME:
				$expires = date('Y-m-d H:i:s', time() + self::SESSION_TOKEN_EXPIRATION_SECONDS_REMEMBER_ME);
				break;
		}

		$sessionRow = $this->DB->sessions()->createRow([
			'user_id' => $userId,
			'token_type' => $tokenType,
			'token_hash' => hash('sha256', $newToken),
			'expires' => $expires,
			'client_info' => $clientInfo,
			'last_used' => date('Y-m-d H:i:s', time())
		]);
		$sessionRow->save();

		$this->DeleteExpiredTokens();

		return $newToken;
	}

	public function DeleteToken(int $tokenType, string $token)
	{
		$this->DB->sessions()->where('token_type = :1 AND token_hash = :2', $tokenType, hash('sha256', $token))->delete();
		$this->DeleteExpiredTokens();
	}

	public function ValidateToken(int $tokenType, string $token)
	{
		if (empty($token))
		{
			return false;
		}

		$sessionRow = $this->DB->sessions()->where('token_type = :1 AND token_hash = :2 AND expires > :3', $tokenType, hash('sha256', $token), date('Y-m-d H:i:s', time()))->fetch();
		if ($sessionRow !== null)
		{
			// This should not change the database file modification time as this is used
			// to determine if REALLY something has changed
			$dbModTime = DatabaseService::GetInstance()->GetDbChangedTime();
			$sessionRow->update([
				'last_used' => date('Y-m-d H:i:s', time())
			]);
			DatabaseService::GetInstance()->SetDbChangedTime($dbModTime);

			return true;
		}
		else
		{
			return false;
		}
	}

	public function GetUserByToken(int $tokenType, string $token)
	{
		$sessionRow = $this->DB->sessions()->where('token_type = :1 AND token_hash = :2', $tokenType, hash('sha256', $token))->fetch();
		if ($sessionRow !== null)
		{
			return $this->DB->users($sessionRow->user_id);
		}

		return null;
	}

	public function GetDefaultUser()
	{
		return $this->DB->users()->orderBy('id')->limit(1)->fetch();
	}

	private function DeleteExpiredTokens()
	{
		$this->DB->sessions()->where('expires < :1', date('Y-m-d H:i:s', time()))->delete();
	}
}
