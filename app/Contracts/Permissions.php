<?php

namespace app\Contracts;

abstract class Permissions
{
    // users
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

    // store permissions
    public const VIEW_ALL_STORES = "view all stores";
    public const VIEW_STORE_DETAILS = "view store details";
    public const CREATE_STORE = "create store";
    public const UPDATE_STORE = "update store";
    public const DELETE_STORE = "delete store";
    public const COMMENT_ON_STORE = "comment on store";
    public const VIEW_STORE_COMMENTS = "view store comments";
    public const VIEW_OWN_STORE = "view own store";

    // store category permissions
    public const VIEW_STORE_CATEGORY = "view store category";
    public const CREATE_STORE_CATEGORY = "create store category";
    public const DELETE_STORE_CATEGORY = "delete store category";

    // comment permissions
    public const UPDATE_COMMENT = "update comment";
    public const DELETE_COMMENT = "delete comment";
    public const LIKE_COMMENT = "like comment";
    public const UNLIKE_COMMENT = "unlike comment";

    // product permissions
    public const VIEW_ALL_PRODUCTS = "view all products";
    public const VIEW_PRODUCT_DETAILS = "view product details";
    public const CREATE_PRODUCTS = "create products";
    public const UPDATE_PRODUCTS = "update products";
    public const DELETE_PRODUCTS = "delete products";
    public const COMMENT_ON_PRODUCTS = "comment on products";
    public const VIEW_PRODUCT_COMMENTS = "view product comments";
    public const VIEW_OWN_PRODUCTS = "view own products";


    // bazaar category permissions
    public const VIEW_BAZAAR_CATEGORY = "view bazaar category";
    public const CREATE_BAZAAR_CATEGORY = "create bazaar category";
    public const DELETE_BAZAAR_CATEGORY = "delete bazaar category";

    // bazaar permissions
    public const VIEW_ALL_BAZAARS = "view all bazaars";
    public const VIEW_BAZAAR_DETAILS = "view bazaar details";
    public const CREATE_BAZAAR = "create bazaar";
    public const UPDATE_BAZAAR = "update bazaar";
    public const DELETE_BAZAAR = "delete bazaar";
    public const COMMENT_ON_BAZAAR = "comment on bazaar";
    public const VIEW_BAZAAR_COMMENTS = "view bazaar comments";
    public const VIEW_OWN_BAZAARS = "view own bazaars";



    public static function all()
    {
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

            Permissions::VIEW_ALL_STORES,
            Permissions::VIEW_STORE_DETAILS,
            Permissions::CREATE_STORE,
            Permissions::UPDATE_STORE,
            Permissions::DELETE_STORE,
            Permissions::COMMENT_ON_STORE,
            Permissions::VIEW_STORE_COMMENTS,
            Permissions::VIEW_OWN_STORE,

            Permissions::VIEW_STORE_CATEGORY,
            Permissions::CREATE_STORE_CATEGORY,
            Permissions::DELETE_STORE_CATEGORY,

            Permissions::UPDATE_COMMENT,
            Permissions::DELETE_COMMENT,
            Permissions::LIKE_COMMENT,
            Permissions::UNLIKE_COMMENT,

            Permissions::VIEW_ALL_PRODUCTS,
            Permissions::VIEW_PRODUCT_DETAILS,
            Permissions::CREATE_PRODUCTS,
            Permissions::UPDATE_PRODUCTS,
            Permissions::DELETE_PRODUCTS,
            Permissions::COMMENT_ON_PRODUCTS,
            Permissions::VIEW_PRODUCT_COMMENTS,
            Permissions::VIEW_OWN_PRODUCTS,

            Permissions::VIEW_BAZAAR_CATEGORY,
            Permissions::CREATE_BAZAAR_CATEGORY,
            Permissions::DELETE_BAZAAR_CATEGORY,

            Permissions::VIEW_ALL_BAZAARS,
            Permissions::VIEW_BAZAAR_DETAILS,
            Permissions::CREATE_BAZAAR,
            Permissions::UPDATE_BAZAAR,
            Permissions::DELETE_BAZAAR,
            Permissions::COMMENT_ON_BAZAAR,
            Permissions::VIEW_BAZAAR_COMMENTS,
            Permissions::VIEW_OWN_BAZAARS
        ];
    }
}
