<?php

use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('shows the edit form with the role and permissions data', function () {
    $this->actingAs(createSuperAdmin());

    $permissions = ensurePermissions(['publish news']);
    $role = Role::firstOrCreate(['name' => 'publisher']);
    $role->syncPermissions($permissions);

    $this->get(route('roles.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/form')
            ->where('role.name', 'publisher')
            ->where('permissions.0.name', 'create_users')
        );
});

it('updates a role and syncs its permissions', function () {
    $this->actingAs(createSuperAdmin());

    $role = Role::firstOrCreate(['name' => 'auditor']);

    $initialPermissions = ensurePermissions(['view logs']);
    $role->syncPermissions($initialPermissions);

    $newPermissions = ensurePermissions(['edit logs', 'export logs']);

    $payload = [
        'name' => 'log-manager',
        'permissions' => $newPermissions->pluck('id')->all(),
    ];

    $this->put(route('roles.update', $role), $payload)
        ->assertRedirect(route('roles.index'));

    $role->refresh();

    expect($role->name)->toBe('log-manager');
    expect($role->permissions->pluck('name')->all())->toEqualCanonicalizing(['edit logs', 'export logs']);
});
