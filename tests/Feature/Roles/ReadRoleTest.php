<?php

use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('lists roles honoring search filters and eager loads permissions', function () {
    $this->actingAs(createSuperAdmin());

    $targetRole = Role::firstOrCreate(['name' => 'auditor']);
    $otherRole = Role::firstOrCreate(['name' => 'operator']);

    $targetRole->syncPermissions(ensurePermissions(['inspect systems']));
    $otherRole->syncPermissions(ensurePermissions(['manage systems']));

    $this->get(route('roles.index', ['filter' => ['search' => 'audit']]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/index')
            ->where('roles.total', 1)
            ->where('roles.data.0.name', 'auditor')
            ->where('roles.data.0.permissions.0.name', 'inspect systems')
        );
});

it('shows a single role with its permissions', function () {
    $this->actingAs(createSuperAdmin());

    $role = Role::firstOrCreate(['name' => 'inspector']);
    $permissions = ensurePermissions(['view logs']);
    $role->syncPermissions($permissions);

    $this->get(route('roles.show', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('role.name', 'inspector')
            ->where('role.permissions.0.name', 'view logs')
        );
});

it('clamps the roles index per-page value to the allowed list', function () {
    $this->actingAs(createSuperAdmin());

    foreach (range(1, 12) as $i) {
        Role::firstOrCreate(['name' => "role-{$i}"]);
    }

    $this->get(route('roles.index', ['per_page' => 999]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/index')
            ->where('roles.per_page', 10)
        );
});

it('sorts the roles list when a sort parameter is present', function () {
    $this->actingAs(createSuperAdmin());

    Role::firstOrCreate(['name' => 'Beta Role']);
    Role::firstOrCreate(['name' => 'Alpha Role']);

    $this->get(route('roles.index', ['sort' => 'name']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/index')
            ->where('roles.data.0.name', 'Alpha Role')
            ->where('roles.data.1.name', 'Beta Role')
        );
});

it('denies access to the roles index for non-superadmins', function () {
    $user = createUserWithPermissions([]);
    $this->actingAs($user);

    $this->get(route('roles.index'))->assertForbidden();
});
