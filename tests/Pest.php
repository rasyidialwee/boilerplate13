<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function (): void {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    })
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
|
| Project-wide helpers shared across feature tests.
|
*/

function permissionRegistrar(): PermissionRegistrar
{
    return app(PermissionRegistrar::class);
}

function resetPermissionCache(): void
{
    permissionRegistrar()->forgetCachedPermissions();
}

function ensurePermissions(array $permissionNames): Collection
{
    resetPermissionCache();

    return collect($permissionNames)->map(fn ($name) => Permission::firstOrCreate(['name' => $name]));
}

function createRoleWithPermissions(string $name, array $permissionNames = []): Role
{
    $role = Role::firstOrCreate(['name' => $name]);

    if ($permissionNames !== []) {
        $permissions = ensurePermissions($permissionNames);
        $role->syncPermissions($permissions);
    }

    return $role;
}

function createSuperAdmin(array $attributes = []): User
{
    resetPermissionCache();

    return User::factory()->asSuperadmin()->create($attributes);
}

function createUserWithPermissions(array $permissionNames, array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    $permissions = ensurePermissions($permissionNames);
    $user->syncPermissions($permissions);

    return $user;
}
