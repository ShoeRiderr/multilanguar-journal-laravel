<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::middleware(['set.language'])->prefix('{locale}')->where(['locale' => '[a-z]{2}'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    })->name('home');

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::resource('categories', App\Http\Controllers\CategoryController::class)
            ->except(['show']);
    });

    require __DIR__.'/settings.php';
});
