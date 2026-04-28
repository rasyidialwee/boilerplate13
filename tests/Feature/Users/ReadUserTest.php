<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('lists users honoring the search scope filter and eager loads roles', function () {
    $this->actingAs(createSuperAdmin());

    $matching = User::factory()->create(['name' => 'Jane Search']);
    $other = User::factory()->create(['name' => 'Mark Other']);

    $matching->assignRole(Role::firstOrCreate(['name' => 'analyst']));
    $other->assignRole(Role::firstOrCreate(['name' => 'support']));

    $this->get(route('users.index', ['filter' => ['search' => 'Jane']]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->where('users.total', 1)
            ->where('users.data.0.name', 'Jane Search')
            ->where('users.data.0.roles.0.name', 'analyst')
        );
});

it('filters the users listing by role name', function () {
    $this->actingAs(createSuperAdmin());

    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $userRole = Role::firstOrCreate(['name' => 'user']);

    $adminUser = User::factory()->create(['name' => 'Admin Person']);
    $regularUser = User::factory()->create(['name' => 'Regular Person']);

    $adminUser->assignRole($adminRole);
    $regularUser->assignRole($userRole);

    $this->get(route('users.index', ['filter' => ['role' => 'admin']]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->where('users.total', 1)
            ->where('users.data.0.id', $adminUser->id)
        );
});

it('sorts the users list when a sort parameter is provided', function () {
    $this->actingAs(createSuperAdmin());

    $bUser = User::factory()->create(['name' => 'Beta User']);
    $aUser = User::factory()->create(['name' => 'Alpha User']);

    $this->get(route('users.index', ['sort' => 'name']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->where('users.data.0.name', 'Alpha User')
            ->where('users.data.1.name', 'Beta User')
        );
});

it('denies access to the users listing when lacking permissions', function () {
    ensurePermissions(['view_users']);

    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))->assertForbidden();
});
