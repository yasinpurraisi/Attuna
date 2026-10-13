<?php

declare(strict_types=1);

use App\Models\Tune;
use App\Models\User;
use App\TuneStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores an uploaded image and tasks when creating a tune', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tunes.store'), [
        'title' => 'Tune with image',
        'status' => TuneStatus::PENDING->value,
        'tasks' => ['Write a melody', 'Arrange the bridge'],
        'image' => UploadedFile::fake()->image('img.png')->size(512),
    ]);

    $response->assertRedirect(route('tunes.index'));
    $tune = Tune::query()->where('title', 'Tune with image')->firstOrFail();
    expect($tune->user_id)->toBe($user->id);
    expect($tune->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($tune->image_path);
    $this->assertDatabaseHas('tasks', [
        'tune_id' => $tune->id,
        'description' => 'Write a melody',
    ]);
    $this->assertDatabaseHas('tasks', [
        'tune_id' => $tune->id,
        'description' => 'Arrange the bridge',
    ]);
});

it('creates a tune when no image is provided', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tunes.store'), [
        'title' => 'Tune without image',
        'status' => TuneStatus::PENDING->value,
    ]);

    $response->assertRedirect(route('tunes.index'));
    $this->assertDatabaseHas('tunes', [
        'user_id' => $user->id,
        'title' => 'Tune without image',
        'image_path' => null,
    ]);
});
