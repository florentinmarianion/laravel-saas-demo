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

        $permissions = [
            // ── Companies ──────────────────────────────────────────────────────
            'companies.create',
            'companies.read',
            'companies.update',
            'companies.delete',

            // ── Users ──────────────────────────────────────────────────────────
            'users.create',
            'users.read',
            'users.update',
            'users.delete',

            // ── Invitations ────────────────────────────────────────────────────
            'invitations.create',
            'invitations.delete',

            // ── Apps ───────────────────────────────────────────────────────────
            'apps.create',
            'apps.read',
            'apps.update',
            'apps.delete',
            'apps.assign',

            // ── Permissions ────────────────────────────────────────────────────
            'permissions.create',
            'permissions.read',
            'permissions.update',
            'permissions.delete',
            'permissions.assign',

            // ── Audit ──────────────────────────────────────────────────────────
            'audit.read',

            // ── Currency Exchange ──────────────────────────────────────────────
            'currency.view',
            'currency.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'member']);
    }
}
