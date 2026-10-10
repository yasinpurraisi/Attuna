<?php

declare(strict_types=1);

namespace App\Models;

use App\TuneStatus;
use Database\Factories\TuneFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tune extends Model
{
    /** @use HasFactory<TuneFactory> */
    use HasFactory;

    // Default attributes
    protected $attributes = [
        'status' => TuneStatus::PENDING->value,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    protected function casts(): array
    {
        return [
            'links' => AsArrayObject::class,
            'status' => TuneStatus::class,
        ];
    }
}
