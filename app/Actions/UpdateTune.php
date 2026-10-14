<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tune;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateTune
{
    /**
     * @param array{
     *     title: string,
     *     status: string,
     *     description?: string|null,
     *     links?: array<int, string>,
     *     steps?: array<int, string>,
     *     tasks?: array<int, array{description: string, completed?: bool|int|string}>,
     *     image?: UploadedFile|null
     * } $attributes
     */
    public function handle(array $attributes, Tune $tune): Tune
    {
        $data = collect($attributes)
            ->only(['title', 'description', 'status', 'links'])
            ->toArray();

        $data['links'] = $attributes['links'] ?? [];

        $image = $attributes['image'] ?? null;

        if ($image instanceof UploadedFile) {
            $imagePath = $image->store('tunes', 'public');

            if ($imagePath === false) {
                throw new RuntimeException('Unable to store the tune image.');
            }

            $data['image_path'] = $imagePath;
        }

        return DB::transaction(function () use ($attributes, $data, $tune): Tune {
            $tune->update($data);

            $tune->tasks()->delete();

            $tasks = collect($attributes['tasks'] ?? [])
                ->map(static fn (array $task): array => [
                    'description' => $task['description'],
                    'completed' => filter_var($task['completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ])
                ->all();

            $tune->tasks()->createMany($tasks);

            return $tune;
        });
    }
}
