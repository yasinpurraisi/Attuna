<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $attributes = [
        'completed' => false,
    ];
    
    public function tune(): BelongsTo
    {
        return $this->belongsTo(Tune::class);
    }
}
