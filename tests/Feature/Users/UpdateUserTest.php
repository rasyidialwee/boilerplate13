<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('shows the edit page with the existing user payload', function () {
    $this->actingAs(createSuperAdmin());

    $user = User::factory()->create();

    $this->get(route('users.edit', $user))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/form')
            ->where('user.id', $user->id)
        );
});

it('includes the available roles when editing a user', function () {
    $this->actingAs(createSuperAdmin());

    $role = Role::firstOrCreate(['name' => 'reviewer']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $this->get(route('users.edit', $user))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/form')
            ->where('roles.0.name', 'reviewer')
        );
});

it('updates a user and syncs their role assignment', function () {
    $this->actingAs(createSuperAdmin());

    $oldRole = Role::firstOrCreate(['name' => 'support']);
    $newRole = Role::firstOrCreate(['name' => 'lead']);

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $user->assignRole($oldRole);

    $payload = [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'password' => 'new-password',
        'role' => $newRole->id,
    ];

    $this->put(route('users.update', $user), $payload)
        ->assertRedirect(route('users.index'));

    $user->refresh();

    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
    expect($user->hasRole('lead'))->toBeTrue();
    expect(password_verify('new-password', $user->password))->toBeTrue();
});
