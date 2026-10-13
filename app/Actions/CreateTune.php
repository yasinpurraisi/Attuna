<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tune;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateTune
{
    /**
     * @param array{
     *     title: string,
     *     status: string,
     *     description?: string|null,
     *     links?: array<int, string>,
     *     steps?: array<int, string>,
     *     tasks?: array<int, string>,
     *     image?: UploadedFile|null
     * } $attributes
     */
    public function handle(array $attributes, ?User $user = null): Tune
    {
        $user ??= Auth::user();

        if (! $user instanceof User) {
            throw new AuthenticationException;
        }

        $data = collect($attributes)
            ->only(['title', 'description', 'status', 'links'])
            ->toArray();

        $image = $attributes['image'] ?? null;

        if ($image instanceof UploadedFile) {
            $imagePath = $image->store('tunes', 'public');

            if ($imagePath === false) {
                throw new RuntimeException('Unable to store the tune image.');
            }

            $data['image_path'] = $imagePath;
        }

        return DB::transaction(function () use ($attributes, $data, $user): Tune {
            $tune = $user->tunes()->create($data);

            $tasks = collect($attributes['tasks'] ?? [])
                ->map(static fn (string $task): array => ['description' => $task])
                ->all();

            $tune->tasks()->createMany($tasks);

            return $tune;
        });
    }
}
