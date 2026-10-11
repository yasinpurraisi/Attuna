<?php

declare(strict_types=1);

use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TuneController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tunes');

Route::get('/tunes', [TuneController::class, 'index'])->name('tunes.index')->middleware('auth');
Route::post('/tunes', [TuneController::class, 'store'])->name('tunes.store')->middleware('auth');
Route::get('/tunes/{tune}', [TuneController::class, 'show'])->name('tunes.show')->middleware('auth');
Route::delete('/tunes/{tune}', [TuneController::class, 'destroy'])->name('tunes.destroy')->middleware('auth');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

Route::get('/login', [SessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth');
