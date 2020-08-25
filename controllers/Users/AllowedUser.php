<?php


namespace Grocy\Controllers\Users;


class AllowedUser extends User
{

    public function hasPermission(string $permission): bool
    {
        return true;
    }
}