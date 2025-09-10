<?php

namespace Database\Seeders;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleHasPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesWithPermissions = [
            Roles::ADMIN => Permissions::all(),
            Roles::INSPECTOR => [
                Permissions::VIEW_OWN_ADDRESSES,
                Permissions::CREATE_ADDRESS,
                Permissions::UPDATE_ADDRESS,
                Permissions::DELETE_ADDRESS,

                Permissions::VIEW_ALL_STORES,
                Permissions::VIEW_STORE_DETAILS,
                Permissions::COMMENT_ON_STORE,
                Permissions::VIEW_STORE_COMMENTS,

                Permissions::UPDATE_COMMENT,
                Permissions::DELETE_COMMENT,
                Permissions::LIKE_COMMENT,
                Permissions::UNLIKE_COMMENT,

                Permissions::VIEW_STORE_CATEGORY,

                Permissions::VIEW_ALL_PRODUCTS,
                Permissions::VIEW_PRODUCT_DETAILS,
                Permissions::COMMENT_ON_PRODUCTS,
                Permissions::VIEW_PRODUCT_COMMENTS,

                Permissions::VIEW_ALL_BAZAARS,
                Permissions::VIEW_BAZAAR_DETAILS,
                Permissions::COMMENT_ON_BAZAAR,
                Permissions::VIEW_BAZAAR_COMMENTS,
                
                Permissions::VIEW_BAZAAR_CATEGORY,
            ],
            Roles::SELLER => [
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

                Permissions::UPDATE_COMMENT,
                Permissions::DELETE_COMMENT,
                Permissions::LIKE_COMMENT,
                Permissions::UNLIKE_COMMENT,

                Permissions::VIEW_STORE_CATEGORY,
                Permissions::CREATE_STORE_CATEGORY,

                Permissions::VIEW_ALL_PRODUCTS,
                Permissions::VIEW_PRODUCT_DETAILS,
                Permissions::CREATE_PRODUCTS,
                Permissions::UPDATE_PRODUCTS,
                Permissions::DELETE_PRODUCTS,
                Permissions::COMMENT_ON_PRODUCTS,
                Permissions::VIEW_PRODUCT_COMMENTS,
                Permissions::VIEW_OWN_PRODUCTS,

                Permissions::VIEW_ALL_BAZAARS,
                Permissions::VIEW_BAZAAR_DETAILS,
                Permissions::CREATE_BAZAAR,
                Permissions::UPDATE_BAZAAR,
                Permissions::DELETE_BAZAAR,
                Permissions::COMMENT_ON_BAZAAR,
                Permissions::VIEW_BAZAAR_COMMENTS,
                Permissions::VIEW_OWN_BAZAARS,

                Permissions::VIEW_BAZAAR_CATEGORY,
                Permissions::CREATE_BAZAAR_CATEGORY,
            ]
        ];

        foreach ($rolesWithPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
