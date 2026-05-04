<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Canonical permission names (snake_case). Keep in sync with policies and Horizon/Telescope gates.
     *
     * @var array<int, string>
     */
    protected array $permissionNames = [
        'view_telescope',
        'view_horizon',
        'manage_system_settings',
        'view_activity_logs',
        'view_users',
        'create_users',
        'edit_users',
        'delete_users',
        'manage_roles',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissionNames as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $superadmin = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $superadmin->syncPermissions($this->permissionNames);

        $admin->syncPermissions([
            'manage_system_settings',
            'view_activity_logs',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ]);

        $user->syncPermissions([]);
    }
}
