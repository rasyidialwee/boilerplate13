<?php

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('document owner can view media through secure route', function () {
    $owner = User::factory()->create();
    $document = Document::factory()->for($owner)->create();
    $upload = UploadedFile::fake()->create('demo.pdf', 50);
    $media = $document->addMedia($upload)->toMediaCollection('default');

    $this->actingAs($owner);

    $response = $this->get(route('media', [
        'type' => 'view',
        'uuid' => $media->uuid,
    ]));

    $response->assertOk();
});

test('other user cannot view another users media', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $document = Document::factory()->for($owner)->create();
    $upload = UploadedFile::fake()->create('demo.pdf', 50);
    $media = $document->addMedia($upload)->toMediaCollection('default');

    $this->actingAs($intruder);

    $this->get(route('media', [
        'type' => 'view',
        'uuid' => $media->uuid,
    ]))->assertForbidden();
});

test('guest cannot view authenticated media', function () {
    $owner = User::factory()->create();
    $document = Document::factory()->for($owner)->create();
    $upload = UploadedFile::fake()->create('demo.pdf', 50);
    $media = $document->addMedia($upload)->toMediaCollection('default');

    $this->get(route('media', [
        'type' => 'view',
        'uuid' => $media->uuid,
    ]))->assertRedirect(route('login'));
});
