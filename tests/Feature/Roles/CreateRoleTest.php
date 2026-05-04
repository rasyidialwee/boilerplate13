<?php

use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('allows superadmins to create roles with permissions', function () {
    $this->actingAs(createSuperAdmin());

    $permissions = ensurePermissions(['manage posts', 'publish posts']);

    $payload = [
        'name' => 'content-manager',
        'permissions' => $permissions->pluck('id')->all(),
    ];

    $this->post(route('roles.store'), $payload)
        ->assertRedirect(route('roles.index'));

    $role = Role::whereName('content-manager')->first();

    expect($role)->not->toBeNull();
    expect($role->permissions->pluck('name')->all())->toEqualCanonicalizing(['manage posts', 'publish posts']);
});

it('shows all permissions when rendering the create form', function () {
    $this->actingAs(createSuperAdmin());

    ensurePermissions(['edit articles']);

    $this->get(route('roles.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/form')
            ->where('permissions.0.name', 'create_users')
        );
});
