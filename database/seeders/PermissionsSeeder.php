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

        // Create permissions
        $this->createPermissions();

        // Create roles and assign existing permissions
        $this->createRoles();

        // Create demo users
        $this->createDemoUsers();
    }

    /**
     * Create permissions.
     */
    protected function createPermissions(): void
    {
        $permissions = [
            'add users',
            'edit users',
            'delete users',
            'view all users',
            'view self',
            'upload documents',
            'view own documents',
            'view any document',
            'delete any document',
            'delete own documents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    /**
     * Create roles and assign existing permissions.
     */
    protected function createRoles(): void
    {
        $client = Role::create(['name' => 'client']);
        $client->givePermissionTo(['upload documents', 'view own documents', 'delete own documents']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['add users', 'edit users', 'delete users', 'view any document', 'delete any document','upload documents', 'view own documents', 'delete own documents']);

        // gets all permissions via Gate::before rule; see AuthServiceProvider
        $superAdmin = Role::create(['name' => 'super-admin']);
    }

    /**
     * Create demo users.
     */
    protected function createDemoUsers(): void
    {
        $users = [
/*             [
                'name' => 'Janez Novak',
                'email' => 'janez.novak@example.com',
                'role' => 'client',
            ], */
/*             [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ], */
            [
                'name' => 'Matej Arh',
                'company_name' => 'Web3 Solutions, Matej Arh s.p., Informacijska tehnologija',
                'email' => 'matej.arh@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Jožica Sodja',
                'company_name' => 'Prava Smer, Jožica Sodja s.p.',
                'email' => 'jozica.sodja@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Bojana Jamnik',
                'company_name' => 'Web3 Solutions, Matej Arh s.p., Informacijska tehnologija',
                'email' => 'bojanabled@gmail.com',
                'role' => 'super-admin',
            ],
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::factory()->create([
                'name' => $userData['name'],
                'company_name' => $userData['company_name'],
                'email' => $userData['email'],
            ]);
            $user->assignRole($userData['role']);
        }
    }
}
