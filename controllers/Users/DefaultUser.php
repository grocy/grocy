<?php


namespace Grocy\Controllers\Users;


use Grocy\Services\DatabaseService;
use LessQL\Result;

class DefaultUser extends User
{
    /**
     * @var \LessQL\Database|null
     */
    protected $db;

    public function __construct()
    {
        $this->db = DatabaseService::getInstance()->GetDbConnection();

    }

    protected function getPermissions(): Result
    {
        return $this->db->permission_check()->where('user_id', GROCY_USER_ID);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->getPermissions()->where('permission_name', $permission)->fetch() != null;
    }
}