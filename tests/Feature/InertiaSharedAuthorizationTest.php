<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests receive null auth.can', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.can', null)
        );
});

test('superadmin receives full can map', function () {
    $this->actingAs(createSuperAdmin());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.can.view_users', true)
            ->where('auth.can.view_activity_logs', true)
            ->where('auth.can.manage_system_settings', true)
            ->where('auth.can.manage_roles', true)
            ->where('auth.can.view_telescope', true)
            ->where('auth.can.view_horizon', true)
        );
});

test('standard user role receives false for elevated abilities', function () {
    $user = User::factory()->asUser()->create();

    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.can.view_users', false)
            ->where('auth.can.view_activity_logs', false)
            ->where('auth.can.manage_system_settings', false)
            ->where('auth.can.manage_roles', false)
            ->where('auth.can.view_telescope', false)
            ->where('auth.can.view_horizon', false)
        );
});
