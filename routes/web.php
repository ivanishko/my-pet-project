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
})->name('home');

// ===== ПОСТЫ (публичный просмотр) =====
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// ===== ФЕДЕРАЦИИ (публичный просмотр) =====
Route::get('/federations', [FederationController::class, 'index'])->name('federations.index');
Route::get('/federations/{federation}', [FederationController::class, 'show'])->name('federations.show');

// ===== ТУРНИРЫ (публичный просмотр) =====
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

// ===== СЕЗОНЫ (публичный просмотр) =====
Route::get('/seasons', [TournamentSeasonController::class, 'index'])->name('seasons.index');
Route::get('/seasons/{season}', [TournamentSeasonController::class, 'show'])->name('seasons.show');

// ============ ЗАЩИЩЕННЫЕ МАРШРУТЫ (только для авторизованных) ============

Route::middleware(['auth'])->group(function () {

    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== УПРАВЛЕНИЕ ПОСТАМИ =====
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ===== УПРАВЛЕНИЕ ФЕДЕРАЦИЯМИ =====
    // ВАЖНО: create ДОЛЖЕН идти ПЕРВЫМ!
    Route::get('/federations/create', [FederationController::class, 'create'])->name('federations.create');
    Route::post('/federations', [FederationController::class, 'store'])->name('federations.store');
    Route::get('/federations/{federation}/edit', [FederationController::class, 'edit'])->name('federations.edit');
    Route::put('/federations/{federation}', [FederationController::class, 'update'])->name('federations.update');
    Route::delete('/federations/{federation}', [FederationController::class, 'destroy'])->name('federations.destroy');

    // ===== УПРАВЛЕНИЕ ТУРНИРАМИ =====
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('/tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournaments.update');
    Route::delete('/tournaments/{tournament}', [TournamentController::class, 'destroy'])->name('tournaments.destroy');

    // ===== УПРАВЛЕНИЕ СЕЗОНАМИ =====
    Route::get('/seasons/create', [TournamentSeasonController::class, 'create'])->name('seasons.create');
    Route::post('/seasons', [TournamentSeasonController::class, 'store'])->name('seasons.store');
    Route::get('/seasons/{season}/edit', [TournamentSeasonController::class, 'edit'])->name('seasons.edit');
    Route::put('/seasons/{season}', [TournamentSeasonController::class, 'update'])->name('seasons.update');
    Route::delete('/seasons/{season}', [TournamentSeasonController::class, 'destroy'])->name('seasons.destroy');
});

// ============ ДАШБОРД (только для авторизованных и верифицированных) ============
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-create', function () {
    return 'Test route works!';
});

// ============ АУТЕНТИФИКАЦИЯ ============
require __DIR__.'/auth.php';
