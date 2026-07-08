<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentSeasonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============ ПУБЛИЧНЫЕ МАРШРУТЫ (доступны всем) ============

// Главная страница
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'posts' => Post::with('user')
            ->latest()
            ->take(10)
            ->get(),
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Просмотр постов (доступно всем)
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Просмотр федераций (доступно всем)
Route::get('/federations', [FederationController::class, 'index'])->name('federations.index');
Route::get('/federations/{federation}', [FederationController::class, 'show'])->name('federations.show');

// Просмотр турниров (доступно всем)
Route::get('/federations/{federation}/tournaments', [TournamentController::class, 'index'])
    ->name('federations.tournaments.index');
Route::get('/federations/{federation}/tournaments/{tournament}', [TournamentController::class, 'show'])
    ->name('federations.tournaments.show');

// ============ ЗАЩИЩЕННЫЕ МАРШРУТЫ (только для авторизованных) ============

Route::middleware(['auth'])->group(function () {

    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== УПРАВЛЕНИЕ ПОСТАМИ =====
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ===== УПРАВЛЕНИЕ ФЕДЕРАЦИЯМИ =====
    Route::post('/federations', [FederationController::class, 'store'])->name('federations.store');
    Route::get('/federations/{federation}/edit', [FederationController::class, 'edit'])->name('federations.edit');
    Route::put('/federations/{federation}', [FederationController::class, 'update'])->name('federations.update');
    Route::delete('/federations/{federation}', [FederationController::class, 'destroy'])->name('federations.destroy');

    // ===== УПРАВЛЕНИЕ ТУРНИРАМИ =====
    Route::post('/federations/{federation}/tournaments', [TournamentController::class, 'store'])
        ->name('federations.tournaments.store');
    Route::get('/federations/{federation}/tournaments/create', [TournamentController::class, 'create'])
        ->name('federations.tournaments.create');
    Route::get('/federations/{federation}/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])
        ->name('federations.tournaments.edit');
    Route::put('/federations/{federation}/tournaments/{tournament}', [TournamentController::class, 'update'])
        ->name('federations.tournaments.update');
    Route::delete('/federations/{federation}/tournaments/{tournament}', [TournamentController::class, 'destroy'])
        ->name('federations.tournaments.destroy');

    // ===== УПРАВЛЕНИЕ СЕЗОНАМИ =====
    Route::post('/tournaments/{tournament}/seasons', [TournamentSeasonController::class, 'store'])
        ->name('tournaments.seasons.store');
    Route::get('/tournaments/{tournament}/seasons/create', [TournamentSeasonController::class, 'create'])
        ->name('tournaments.seasons.create');
    Route::get('/tournaments/{tournament}/seasons/{season}/edit', [TournamentSeasonController::class, 'edit'])
        ->name('tournaments.seasons.edit');
    Route::put('/tournaments/{tournament}/seasons/{season}', [TournamentSeasonController::class, 'update'])
        ->name('tournaments.seasons.update');
    Route::delete('/tournaments/{tournament}/seasons/{season}', [TournamentSeasonController::class, 'destroy'])
        ->name('tournaments.seasons.destroy');
});

// ============ ДАШБОРД (только для авторизованных и верифицированных) ============
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============ АУТЕНТИФИКАЦИЯ ============
require __DIR__.'/auth.php';
