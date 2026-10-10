<?php

declare(strict_types=1);

use App\Models\Tune;
use App\Models\User;

test('it belongs to a user', function () {
    $tune = Tune::factory()->create();
    expect($tune->user)->toBeInstanceOf(User::class);
});
