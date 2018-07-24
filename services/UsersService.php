<?php

namespace Grocy\Services;

class UsersService extends BaseService
{
	public function CreateUser(string $username, string $firstName, string $lastName, string $password)
	{
		$newUserRow = $this->Database->users()->createRow(array(
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

		$user = $this->Database->users($userId);
		$user->update(array(
			'username' => $username,
			'first_name' => $firstName,
			'last_name' => $lastName,
			'password' => password_hash($password, PASSWORD_DEFAULT)
		));
	}

	public function DeleteUser($userId)
	{
		$row = $this->Database->users($args['userId']);
		$row->delete();
		$success = $row->isClean();
		return $this->ApiResponse(array('success' => $success));
	}

	private function UserExists($userId)
	{
		$userRow = $this->Database->users()->where('id = :1', $userId)->fetch();
		return $userRow !== null;
	}
}
