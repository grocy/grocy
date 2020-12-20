<?php

namespace Grocy\Services;

class UsersService extends BaseService
{
	public function CreateUser(string $username, string $firstName, string $lastName, string $password, string $pictureFileName = null)
	{
		$newUserRow = $this->getDatabase()->users()->createRow([
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'picture_file_name' => $pictureFileName
		]);
		$newUserRow = $newUserRow->save();
		$permList = [];

		foreach ($this->getDatabase()->permission_hierarchy()->where('name', GROCY_DEFAULT_PERMISSIONS)->fetchAll() as $perm)
		{
			$permList[] = [
				'user_id' => $newUserRow->id,
				'permission_id' => $perm->id
			];
		}

		$this->getDatabase()->user_permissions()->insert($permList);

		return $newUserRow;
	}

	public function DeleteUser($userId)
	{
		$row = $this->getDatabase()->users($userId);
		$row->delete();
	}

	public function EditUser(int $userId, string $username, string $firstName, string $lastName, string $password, string $pictureFileName = null)
	{
		if (!$this->UserExists($userId))
		{
			throw new \Exception('User does not exist');
		}

		$user = $this->getDatabase()->users($userId);
		$user->update([
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'picture_file_name' => $pictureFileName
		]);
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
			// Use the configured default values for a missing setting, otherwise return NULL
			global $GROCY_DEFAULT_USER_SETTINGS;
			if (array_key_exists($settingKey, $GROCY_DEFAULT_USER_SETTINGS))
			{
				return $GROCY_DEFAULT_USER_SETTINGS[$settingKey];
			}
			else
			{
				return null;
			}
		}
	}

	public function GetUserSettings($userId)
	{
		$settings = [];

		$settingRows = $this->getDatabase()->user_settings()->where('user_id = :1', $userId)->fetchAll();
		foreach ($settingRows as $settingRow)
		{
			$settings[$settingRow->key] = $settingRow->value;
		}

		// Use the configured default values for all missing settings
		global $GROCY_DEFAULT_USER_SETTINGS;
		return array_merge($GROCY_DEFAULT_USER_SETTINGS, $settings);
	}

	public function GetUsersAsDto(): \LessQL\Result
	{
		return $this->getDatabase()->users_dto();
	}

	public function SetUserSetting($userId, $settingKey, $settingValue)
	{
		$settingRow = $this->getDatabase()->user_settings()->where('user_id = :1 AND key = :2', $userId, $settingKey)->fetch();

		if ($settingRow !== null)
		{
			$settingRow->update([
				'value' => $settingValue,
				'row_updated_timestamp' => date('Y-m-d H:i:s')
			]);
		}
		else
		{
			$settingRow = $this->getDatabase()->user_settings()->createRow([
				'user_id' => $userId,
				'key' => $settingKey,
				'value' => $settingValue
			]);
			$settingRow->save();
		}
	}

	public function DeleteUserSetting($userId, $settingKey)
	{
		$this->getDatabase()->user_settings()->where('user_id = :1 AND key = :2', $userId, $settingKey)->delete();
	}

	private function UserExists($userId)
	{
		$userRow = $this->getDatabase()->users()->where('id = :1', $userId)->fetch();
		return $userRow !== null;
	}
}
