<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CompaniesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $this->createPermissions();

    }


    /**
     * Create permissions.
     */
    protected function createPermissions(): void
    {
        $permissions = [
            'view own companies',
            'view any company',
            'delete any company',
            'delete own companies',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $client = Role::where(['name' => 'client'])->first();
        $client->givePermissionTo(['view own companies', 'delete own companies']);

        $admin = Role::where(['name' => 'admin'])->first();
        $admin->givePermissionTo(['view any company', 'delete any company']);
    }
}

