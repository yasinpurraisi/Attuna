<?php

declare(strict_types=1);

use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');


Route::get('/login', [SessionController::class, 'create'])->middleware('guest');
Route::post('/login', [SessionController::class, 'store'])->middleware('guest');

Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth');
