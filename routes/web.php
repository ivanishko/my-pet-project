<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentSeasonController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\SeasonTeamController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ScheduleController;
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
Route::get('/seasons/all', [TournamentSeasonController::class, 'all'])->name('seasons.all'); // Все сезоны
Route::get('/seasons/{season}', [TournamentSeasonController::class, 'show'])->name('seasons.show');

// ===== КОМАНДЫ (публичный просмотр) =====
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('/federations/{federation}/teams', [TeamController::class, 'indexByFederation'])->name('federations.teams.index');





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

    Route::post('/federations', [FederationController::class, 'store'])->name('federations.store');
    Route::get('/federations/{federation}/edit', [FederationController::class, 'edit'])->name('federations.edit');
    Route::put('/federations/{federation}', [FederationController::class, 'update'])->name('federations.update');
    Route::delete('/federations/{federation}', [FederationController::class, 'destroy'])->name('federations.destroy');

    // ===== УПРАВЛЕНИЕ ТУРНИРАМИ =====

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

    // ===== УПРАВЛЕНИЕ КОМАНДАМИ =====
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

    // ===== УПРАВЛЕНИЕ этапами сезонов ====
    Route::post('/stages', [StageController::class, 'store'])->name('stages.store');
    Route::put('/stages/{stage}', [StageController::class, 'update'])->name('stages.update');
    Route::delete('/stages/{stage}', [StageController::class, 'destroy'])->name('stages.destroy');

    // Управление командами в сезоне
    Route::get('/seasons/{season}/teams', [SeasonTeamController::class, 'index'])->name('seasons.teams.index');
    Route::post('/seasons/teams', [SeasonTeamController::class, 'store'])->name('seasons.teams.store');
    Route::delete('/seasons/{season}/teams/{team}', [SeasonTeamController::class, 'destroy'])->name('seasons.teams.destroy');
    Route::post('/seasons/teams/multiple', [SeasonTeamController::class, 'storeMultiple'])->name('seasons.teams.store.multiple');

    Route::get('/stages/{stage}', [StageController::class, 'show'])->name('stages.show');

    // ===== УПРАВЛЕНИЕ ТУРАМИ =====
    Route::get('/stages/{stage}/rounds', [RoundController::class, 'index'])->name('stages.rounds.index');
    Route::post('/rounds', [RoundController::class, 'store'])->name('rounds.store');
    Route::post('/rounds/{round}/games', [RoundController::class, 'addGame'])->name('rounds.games.store');
    Route::put('/rounds/{round}', [RoundController::class, 'update'])->name('rounds.update');
    Route::delete('/rounds/{round}', [RoundController::class, 'destroy'])->name('rounds.destroy');

    // ===== УПРАВЛЕНИЕ ИГРАМИ =====
    Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::put('/rounds/{round}/games', [GameController::class, 'updateMultiple'])->name('rounds.games.update');


    Route::post('/seasons/schedule', [ScheduleController::class, 'store'])->name('seasons.schedule.store');
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
