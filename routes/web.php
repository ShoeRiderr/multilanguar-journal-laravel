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

    Route::get('posts/{post}/view', [\App\Http\Controllers\PostViewController::class, 'view'])->name('posts.view');

    Route::get('dashboard', function () {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('home', ['locale' => app()->getLocale()]);
        }
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('posts', App\Http\Controllers\PostController::class)
        ->only(['show', 'index']);
    Route::middleware(['auth', 'verified'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)
                ->except(['show']);
            Route::resource('languages', App\Http\Controllers\Admin\LanguageController::class)
                ->except(['show']);
            Route::resource('posts', App\Http\Controllers\Admin\PostController::class)
                ->except(['show']);
            Route::resource('pages', App\Http\Controllers\Admin\PageController::class)
                ->except(['show']);
    });

    require __DIR__.'/settings.php';
});
