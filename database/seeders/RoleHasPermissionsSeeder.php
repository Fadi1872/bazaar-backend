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
            Roles::ADMIN => [
                Permissions::VIEW_USERS,
                Permissions::CREATE_USERS,
                Permissions::UPDATE_USERS,
                Permissions::DELETE_USERS,
                Permissions::ASSIGN_ROLES,

                Permissions::VIEW_ALL_ADDRESSES,
                Permissions::CREATE_ADDRESS,
                Permissions::UPDATE_ADDRESS,
                Permissions::DELETE_ADDRESS,
            ],
            Roles::INSPECTOR => [
                Permissions::VIEW_OWN_ADDRESSES,
                Permissions::CREATE_ADDRESS,
                Permissions::UPDATE_ADDRESS,
                Permissions::DELETE_ADDRESS
            ],
            Roles::SELLER => []
        ];

        foreach ($rolesWithPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
