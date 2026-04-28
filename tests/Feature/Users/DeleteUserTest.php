<?php

use App\Models\User;

it('deletes other users but prevents self-deletion', function () {
    $admin = createSuperAdmin();
    $this->actingAs($admin);

    $target = User::factory()->create();

    $this->delete(route('users.destroy', $target))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $target->id]);

    $this->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors('error');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
