<?php


namespace Grocy\Controllers\Users;


class LockedUser extends User
{

    public function hasPermission(string $permission): bool
    {
        return false;
    }
}