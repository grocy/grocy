<?php

namespace Grocy\Services;

class ApiKeyService extends BaseService
{
	/**
	 * @return boolean
	 */
	public function IsValidApiKey($apiKey)
	{
		if ($apiKey === null || empty($apiKey))
		{
			return false;
		}
		else
		{
			$apiKeyRow = $this->Database->api_keys()->where('api_key = :1 AND expires > :2', $apiKey, date('Y-m-d H:i:s', time()))->fetch();
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
	public function CreateApiKey()
	{
		$newApiKey = $this->GenerateApiKey();
		
		$apiKeyRow = $this->Database->api_keys()->createRow(array(
			'api_key' => $newApiKey,
			'user_id' => GROCY_USER_ID,
			'expires' => '2999-12-31 23:59:59' // Default is that API keys expire never
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

	private function GenerateApiKey()
	{
		return RandomString(50);
	}
}
