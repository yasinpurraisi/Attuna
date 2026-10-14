<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tune;
use App\Models\User;

class TunePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function workWith(User $user, Tune $tune): bool
    {
        return $tune->user->is($user);
    }
}
