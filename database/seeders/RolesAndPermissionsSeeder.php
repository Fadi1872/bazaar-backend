<?php

namespace Database\Seeders;

use app\Contracts\Permissions;
use app\Contracts\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Roles::all();
        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }

        $permissions = Permissions::all();
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }
    }
}
