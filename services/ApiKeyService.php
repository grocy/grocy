<?php

namespace Grocy\Services;

class ApiKeyService extends BaseService
{
	const API_KEY_TYPE_DEFAULT = 'default';
	const API_KEY_TYPE_SPECIAL_PURPOSE_CALENDAR_ICAL = 'special-purpose-calendar-ical';

	/**
	 * @return boolean
	 */
	public function IsValidApiKey($apiKey, $keyType = self::API_KEY_TYPE_DEFAULT)
	{
		if ($apiKey === null || empty($apiKey))
		{
			return false;
		}
		else
		{
			$apiKeyRow = $this->Database->api_keys()->where('api_key = :1 AND expires > :2 AND key_type = :3', $apiKey, date('Y-m-d H:i:s', time()), $keyType)->fetch();
			if ($apiKeyRow !== null)
			{
				$apiKeyRow->update(array(
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
	public function CreateApiKey($keyType = self::API_KEY_TYPE_DEFAULT)
	{
		$newApiKey = $this->GenerateApiKey();
		
		$apiKeyRow = $this->Database->api_keys()->createRow(array(
			'api_key' => $newApiKey,
			'user_id' => GROCY_USER_ID,
			'expires' => '2999-12-31 23:59:59', // Default is that API keys expire never
			'key_type' => $keyType
		));
		$apiKeyRow->save();

		return $newApiKey;
	}

	public function RemoveApiKey($apiKey)
	{
		$this->Database->api_keys()->where('api_key', $apiKey)->delete();
	}

	public function GetApiKeyId($apiKey)
	{
		$apiKey = $this->Database->api_keys()->where('api_key', $apiKey)->fetch();
		return $apiKey->id;
	}

	public function GetUserByApiKey($apiKey)
	{
		$apiKeyRow = $this->Database->api_keys()->where('api_key', $apiKey)->fetch();
		if ($apiKeyRow !== null)
		{
			return $this->Database->users($apiKeyRow->user_id);
		}
		return null;
	}

	// Returns any valid key for $keyType,
	// not allowed for key type "default"
	public function GetOrCreateApiKey($keyType)
	{
		if ($keyType === self::API_KEY_TYPE_DEFAULT)
		{
			return null;
		}
		else
		{
			$apiKeyRow = $this->Database->api_keys()->where('key_type = :1 AND expires > :2', $keyType, date('Y-m-d H:i:s', time()))->fetch();
			if ($apiKeyRow !== null)
			{
				return $apiKeyRow->api_key;
			}
			else
			{
				return $this->CreateApiKey($keyType);
			}
		}
	}

	private function GenerateApiKey()
	{
		return RandomString(50);
	}
}
