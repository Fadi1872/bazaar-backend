<?php

namespace app\Contracts;

abstract class Permissions {
    //users
    public const VIEW_USERS = "view users";
    public const CREATE_USERS = "create users";
    public const UPDATE_USERS = "update users";
    public const DELETE_USERS = "delete users";
    public const ASSIGN_ROLES = "assign roles";

    // address permissions
    public const VIEW_ALL_ADDRESSES = "view all addresses";
    public const VIEW_OWN_ADDRESSES = "view own addresses";
    public const CREATE_ADDRESS = "create address";
    public const UPDATE_ADDRESS = "update address";
    public const DELETE_ADDRESS = "delete address";
    



    public static function all() {
        return [
            Permissions::VIEW_USERS,
            Permissions::CREATE_USERS,
            Permissions::UPDATE_USERS,
            Permissions::DELETE_USERS,
            Permissions::ASSIGN_ROLES,

            Permissions::VIEW_ALL_ADDRESSES,
            Permissions::VIEW_OWN_ADDRESSES,
            Permissions::CREATE_ADDRESS,
            Permissions::UPDATE_ADDRESS,
            Permissions::DELETE_ADDRESS,
        ];
    }
}