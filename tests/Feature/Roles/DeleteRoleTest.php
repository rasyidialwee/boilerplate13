<?php

use Spatie\Permission\Models\Role;

it('deletes a role and clears the permission cache', function () {
    $this->actingAs(createSuperAdmin());

    $role = Role::firstOrCreate(['name' => 'transient-role']);

    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('roles.index'));

    expect(Role::whereName('transient-role')->exists())->toBeFalse();
});
