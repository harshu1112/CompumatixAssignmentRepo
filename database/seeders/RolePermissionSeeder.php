<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for tickets
        $ticketPermissions = [
            'view all tickets',
            'view assigned tickets',
            'create tickets',
            'update tickets',
            'update assigned tickets',
            'delete tickets',
            'assign tickets',
            'change ticket status',
        ];

        // Create permissions for comments
        $commentPermissions = [
            'add comments',
            'update own comments',
            'delete own comments',
        ];

        // Create permissions for dashboard
        $dashboardPermissions = [
            'access dashboard',
        ];

        // Combine all permissions
        $allPermissions = array_merge($ticketPermissions, $commentPermissions, $dashboardPermissions);

        // Create all permissions (only if they don't exist)
        $createdPermissions = [];
        foreach ($allPermissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate(['name' => $permission]);
        }

        // Clear cache again after creating permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create or get Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'view all tickets',
            'create tickets',
            'update tickets',
            'delete tickets',
            'assign tickets',
            'change ticket status',
            'add comments',
            'update own comments',
            'delete own comments',
            'access dashboard',
        ]);

        // Create or get Staff role with limited permissions
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions([
            'view assigned tickets',
            'update assigned tickets',
            'change ticket status',
            'add comments',
            'update own comments',
            'delete own comments',
        ]);
    }
}
