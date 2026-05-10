<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'manage-companies',
            'manage-users',
            'send-invitations',
            'view-invitations',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Roles
        Role::create(['name' => 'admin'])
            ->givePermissionTo($permissions);

        Role::create(['name' => 'member'])
            ->givePermissionTo(['view-invitations']);
    }
}