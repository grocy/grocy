<?php

namespace Grocy\Controllers\Users;

use Grocy\Services\DatabaseService;
use LessQL\Result;

class User
{
    const PERMISSION_ADMIN = 'ADMIN';
    const PERMISSION_CREATE_USER = 'CREATE_USER';
    const PERMISSION_EDIT_USER = 'EDIT_USER';
    const PERMISSION_READ_USER = 'READ_USER';
    const PERMISSION_EDIT_SELF = 'EDIT_SELF';
    const PERMISSION_BATTERY_UNDO_TRACK_CHARGE_CYCLE = 'BATTERY_UNDO_TRACK_CHARGE_CYCLE';
    const PERMISSION_BATTERY_TRACK_CHARGE_CYCLE = 'BATTERY_TRACK_CHARGE_CYCLE';
    const PERMISSION_CHORE_TRACK = 'CHORE_TRACK';
    const PERMISSION_CHORE_TRACK_OTHERS = 'CHORE_TRACK_OTHERS';
    const PERMISSION_CHORE_EDIT = 'CHORE_EDIT';
    const PERMISSION_CHORE_UNDO = 'CHORE_UNDO';
    const PERMISSION_UPLOAD_FILE = 'UPLOAD_FILE';
    const PERMISSION_DELETE_FILE = 'DELETE_FILE';
    const PERMISSION_MASTER_DATA_EDIT = 'MASTER_DATA_EDIT';
    const PERMISSION_TASKS_UNDO = 'TASKS_UNDO';
    const PERMISSION_TASKS_MARK_COMPLETED = 'TASKS_MARK_COMPLETED';
    const PERMISSION_STOCK_TRANSFER = 'STOCK_TRANSFER';
    const PERMISSION_STOCK_EDIT = 'STOCK_EDIT';
    const PERMISSION_PRODUCT_CONSUME = 'PRODUCT_CONSUME';
    const PERMISSION_STOCK_CORRECTION = 'STOCK_CORRECTION';
    const PERMISSION_PRODUCT_OPEN = 'PRODUCT_OPEN';
    const PERMISSION_SHOPPINGLIST_ITEMS_ADD = 'SHOPPINGLIST_ITEMS_ADD';
    const PERMISSION_SHOPPINGLIST_ITEMS_DELETE = 'SHOPPINGLIST_ITEMS_DELETE';
    const PERMISSION_PRODUCT_PURCHASE = 'PRODUCT_PURCHASE';

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
        return $this->db->user_permissions_resolved()->where('user_id', GROCY_USER_ID);
    }

    public function hasPermission(string $permission): bool
    {
        // global $PERMISSION_CACHE;
        // if(isset($PERMISSION_CACHE[$permission]))
        //    return $PERMISSION_CACHE[$permission];
        return $this->getPermissions()->where('permission_name', $permission)->fetch() !== null;
    }

    public static function checkPermission($request, string ...$permissions): void
    {
        $user = new User();
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                throw new PermissionMissingException($request, $permission);
            }
        }

    }

    public function getPermissionList()
    {
        return $this->db->uihelper_user_permissions()->where('user_id', GROCY_USER_ID);
    }

    public static function hasPermissions(string ...$permissions)
    {
        $user = new User();
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public static function PermissionList()
    {
        $user = new User();
        return $user->getPermissionList();
    }
}
