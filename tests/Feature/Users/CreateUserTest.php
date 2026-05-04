<?php

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

it('allows a superadmin to create users with roles and dispatches the event', function () {
    $this->actingAs(createSuperAdmin());
    Event::fake([UserCreated::class]);

    $role = Role::firstOrCreate(['name' => 'manager']);

    $response = $this->post(route('users.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'role' => $role->id,
    ]);

    $response->assertRedirect(route('users.index'));

    $user = User::whereEmail('jane@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->roles->first()->id)->toBe($role->id);

    Event::assertDispatched(UserCreated::class, fn ($event) => $event->user->is($user));
});

it('shows the available roles when rendering the create form', function () {
    $this->actingAs(createSuperAdmin());

    Role::firstOrCreate(['name' => 'editor']);

    $this->get(route('users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/form')
            ->where('roles.0.name', 'admin')
        );
});
