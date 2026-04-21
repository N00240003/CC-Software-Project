<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SaveGameController;
use App\Http\Controllers\LogBookController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/game', [GameController::class, 'index'])->name('game'); //serve game view

    Route::get('/game', [GameController::class, 'index'])->name('game'); // fetch slots
    Route::get('/saves', [SaveGameController::class, 'savesPage'])->name('saves');
    Route::get('/save-game', [SaveGameController::class, 'index']);
    Route::post('/save-game', [SaveGameController::class, 'store']); // save a slot
    Route::delete('/save-game/{saveGame}', [SaveGameController::class, 'destroy']); // delete

    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
});

require __DIR__ . '/auth.php';
