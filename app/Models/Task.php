<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $attributes = [
        'completed' => false,
    ];

    public function tune(): BelongsTo
    {
        return $this->belongsTo(Tune::class);
    }

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
        ];
    }
}
