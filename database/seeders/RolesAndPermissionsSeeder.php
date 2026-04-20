<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public const string ROLE_ADMIN = 'admin';
    public const string ROLE_MANAGER = 'manager';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::query()->firstOrCreate(['name' => self::ROLE_ADMIN]);
        Role::query()->firstOrCreate(['name' => self::ROLE_MANAGER]);
    }
}
