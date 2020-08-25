<?php

namespace Grocy\Controllers\Users;

abstract class User
{
    const PERMISSION_ADMIN = 'ADMIN';
    const PERMISSION_CREATE_USER = 'CREATE_USER';
    const PERMISSION_EDIT_USER = 'EDIT_USER';
    const PERMISSION_READ_USER = 'READ_USER';
    const PERMISSION_EDIT_SELF = 'EDIT_SELF';

    public abstract function hasPermission(string $permission): bool;

    public static function checkPermission($request, string ...$permissions): void
    {
        $user_class = GROCY_USER_CLASS;
        $user = new $user_class();
        assert($user instanceof User, 'Please check the Setting USER_CLASS: It should be an implementation of User');
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                throw new PermissionMissingException($request, $permission);
            }
        }
    }


}

