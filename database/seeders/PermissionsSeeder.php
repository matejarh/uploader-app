<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'add users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'view all users']);
        Permission::create(['name' => 'view self']);
        Permission::create(['name' => 'upload documents']);
        Permission::create(['name' => 'view own documents']);
        Permission::create(['name' => 'view any document']);
        Permission::create(['name' => 'delete any document']);
        Permission::create(['name' => 'delete own documents']);

        $client = Role::create(['name' => 'client']);
        $client->givePermissionTo('upload documents');
        $client->givePermissionTo('view own documents');
        $client->givePermissionTo('delete own documents');

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('add users');
        $admin->givePermissionTo('edit users');
        $admin->givePermissionTo('delete users');
        $admin->givePermissionTo('view any document');
        $admin->givePermissionTo('delete any document');

        $superAdmin = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Janez Novak',
            'email' => 'janez.novak@example.com',
        ]);
        $user->assignRole($client);

        $user = \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($admin);

        $user = \App\Models\User::factory()->create([
            'name' => 'Super-Admin',
            'email' => 'super-admin@example.com',
        ]);
        $user->assignRole($superAdmin);
    }
}
