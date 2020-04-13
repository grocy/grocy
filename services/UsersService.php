<?php

namespace Grocy\Services;

class UsersService extends BaseService
{
	public function CreateUser(string $username, string $firstName, string $lastName, string $password)
	{
		$newUserRow = $this->getDatabase()->users()->createRow(array(
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password' => password_hash($password, PASSWORD_DEFAULT)
		));
		$newUserRow->save();
	}

	public function EditUser(int $userId, string $username, string $firstName, string $lastName, string $password)
	{
		if (!$this->UserExists($userId))
		{
			throw new \Exception('User does not exist');
		}

		$user = $this->getDatabase()->users($userId);
		$user->update(array(
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password' => password_hash($password, PASSWORD_DEFAULT)
		));
	}

	public function DeleteUser($userId)
	{
		$row = $this->getDatabase()->users($userId);
		$row->delete();
	}

	public function GetUsersAsDto()
	{
		$users = $this->getDatabase()->users();
		$returnUsers = array();
		foreach ($users as $user)
		{
			unset($user->password);
			$user->display_name = GetUserDisplayName($user);
			$returnUsers[] = $user;
		}
		return $returnUsers;
	}

	public function GetUserSetting($userId, $settingKey)
	{
		$settingRow = $this->getDatabase()->user_settings()->where('user_id = :1 AND key = :2', $userId, $settingKey)->fetch();
		if ($settingRow !== null)
		{
			return $settingRow->value;
		}
		else
		{
			return null;
		}
	}

	public function GetUserSettings($userId)
	{
		$settings = array();

		$settingRows = $this->getDatabase()->user_settings()->where('user_id = :1', $userId)->fetchAll();
		foreach ($settingRows as $settingRow)
		{
			$settings[$settingRow->key] = $settingRow->value;
		}

		// Use the configured default values for all missing settings
		global $GROCY_DEFAULT_USER_SETTINGS;
		return array_merge($GROCY_DEFAULT_USER_SETTINGS, $settings);
	}

	public function SetUserSetting($userId, $settingKey, $settingValue)
	{
		$settingRow = $this->getDatabase()->user_settings()->where('user_id = :1 AND key = :2', $userId, $settingKey)->fetch();
		if ($settingRow !== null)
		{
			$settingRow->update(array(
				'value' => $settingValue,
				'row_updated_timestamp' => date('Y-m-d H:i:s')
			));
		}
		else
		{
			$settingRow = $this->getDatabase()->user_settings()->createRow(array(
				'user_id' => $userId,
				'key' => $settingKey,
				'value' => $settingValue
			));
			$settingRow->save();
		}
	}

	private function UserExists($userId)
	{
		$userRow = $this->getDatabase()->users()->where('id = :1', $userId)->fetch();
		return $userRow !== null;
	}
}
