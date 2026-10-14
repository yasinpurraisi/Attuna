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
        'links' => ['https://example.com/song', 'https://example.com/notes'],
        'tasks' => [
            ['description' => 'Write a melody', 'completed' => '0'],
            ['description' => 'Arrange the bridge', 'completed' => '1'],
        ],
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
        'completed' => false,
    ]);
    $this->assertDatabaseHas('tasks', [
        'tune_id' => $tune->id,
        'description' => 'Arrange the bridge',
        'completed' => true,
    ]);
    expect($tune->links->getArrayCopy())->toBe([
        'https://example.com/song',
        'https://example.com/notes',
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

it('rejects task values that are not task objects', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tunes.store'), [
        'title' => 'Tune with invalid task',
        'status' => TuneStatus::PENDING->value,
        'tasks' => ['Write a melody'],
    ]);

    $response->assertSessionHasErrors('tasks.0');
    $this->assertDatabaseMissing('tunes', [
        'user_id' => $user->id,
        'title' => 'Tune with invalid task',
    ]);
});

it('updates a tune links and tasks', function () {
    $user = User::factory()->create();
    $tune = $user->tunes()->create([
        'title' => 'Original tune',
        'status' => TuneStatus::PENDING,
        'links' => ['https://example.com/old'],
    ]);
    $tune->tasks()->create([
        'description' => 'Old task',
    ]);

    $response = $this->actingAs($user)->patch(route('tunes.update', $tune), [
        'title' => 'Updated tune',
        'status' => TuneStatus::IN_PROGRESS->value,
        'links' => ['https://example.com/new'],
        'tasks' => [
            ['description' => 'New task', 'completed' => '1'],
        ],
    ]);

    $response->assertRedirect();
    $tune->refresh();
    expect($tune->title)->toBe('Updated tune');
    expect($tune->links->getArrayCopy())->toBe(['https://example.com/new']);
    $this->assertDatabaseHas('tasks', [
        'tune_id' => $tune->id,
        'description' => 'New task',
        'completed' => true,
    ]);
    $this->assertDatabaseMissing('tasks', [
        'tune_id' => $tune->id,
        'description' => 'Old task',
    ]);
});

it('clears tune links and tasks when the update contains none', function () {
    $user = User::factory()->create();
    $tune = $user->tunes()->create([
        'title' => 'Tune with details',
        'status' => TuneStatus::PENDING,
        'links' => ['https://example.com/song'],
    ]);
    $tune->tasks()->create([
        'description' => 'Remove this task',
    ]);

    $response = $this->actingAs($user)->patch(route('tunes.update', $tune), [
        'title' => 'Tune with details',
        'status' => TuneStatus::PENDING->value,
    ]);

    $response->assertRedirect();
    expect($tune->refresh()->links->getArrayCopy())->toBe([]);
    $this->assertDatabaseMissing('tasks', [
        'tune_id' => $tune->id,
        'description' => 'Remove this task',
    ]);
});

it('renders the tune modal with array links and indexed task inputs', function () {
    $user = User::factory()->create();
    $tune = $user->tunes()->create([
        'title' => 'Tune with details',
        'status' => TuneStatus::PENDING,
        'links' => ['https://example.com/song'],
    ]);

    $this->withoutVite();
    $response = $this->actingAs($user)->get(route('tunes.show', $tune));

    $response->assertOk()
        ->assertSee('links: JSON.parse(', false)
        ->assertSee('tasks[${index}][description]', false);
});
