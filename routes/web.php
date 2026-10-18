<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TuneController;
use App\Http\Controllers\TuneImageController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\FriendshipController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tunes');

Route::get('/tunes', [TuneController::class, 'index'])->name('tunes.index')->middleware('auth');
Route::post('/tunes', [TuneController::class, 'store'])->name('tunes.store')->middleware('auth');
Route::get('/tunes/{tune}', [TuneController::class, 'show'])->name('tunes.show')->middleware('auth');
Route::delete('/tunes/{tune}', [TuneController::class, 'destroy'])->name('tunes.destroy')->middleware('auth');

Route::patch('/tunes/{tune}', [TuneController::class, 'update'])->name('tunes.update')->middleware('auth');

Route::delete('/tunes/{tune}/image', [TuneImageController::class, 'destroy'])->name('tunes.image.destroy')->middleware('auth');

Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware('auth');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/login', [SessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('auth');


// TODO: Add authorization before allowing deletion
Route::middleware('auth')->group(function () {
    // Current user's profile
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // User directory and individual user profile
    Route::get('/users', [UserSearchController::class, 'index'])
        ->name('users.index');

    Route::get('/users/{user}', [UserSearchController::class, 'show'])
        ->name('users.show');

    // Friendship management
    Route::post('/friendships/{user}', [FriendshipController::class, 'store'])
        ->name('friendships.store');

    Route::post('/friendships/{friendship}/accept', [FriendshipController::class, 'accept'])
        ->name('friendships.accept');

    Route::delete('/friendships/{friendship}', [FriendshipController::class, 'destroy'])
        ->name('friendships.destroy');

    // Friend lists and requests
    Route::get('/friends', [FriendshipController::class, 'friends'])
        ->name('friends.index');

    Route::get('/friend-requests', [FriendshipController::class, 'requests'])
        ->name('friendships.requests');
});
