<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Controllers\FederationController;
use App\Http\Controllers\TournamentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'posts' => Post::with('user')
            ->latest()
            ->take(10) // Ограничиваем количество постов
            ->get(),
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

});

Route::middleware(['auth', 'verified'])->group(function () {
    // Главная страница федераций
    Route::get('/federations', [FederationController::class, 'index'])
         ->name('federations.index');

    // Создание федерации
    Route::post('/federations', [FederationController::class, 'store'])
         ->name('federations.store');
	Route::get('/federations/{federation}', [FederationController::class, 'show'])
	     ->name('federations.show');
    // Редактирование федерации
    Route::get('/federations/{federation}/edit', [FederationController::class, 'edit'])
         ->name('federations.edit');

    // Обновление федерации
    Route::put('/federations/{federation}', [FederationController::class, 'update'])
         ->name('federations.update');

    // Удаление федерации
    Route::delete('/federations/{federation}', [FederationController::class, 'destroy'])
         ->name('federations.destroy');
});

Route::prefix('federations/{federation}')->group(function () {
    Route::resource('tournaments', TournamentController::class)
        ->names([
            'index' => 'federations.tournaments.index',
            'create' => 'federations.tournaments.create',
            'store' => 'federations.tournaments.store',
            'edit' => 'federations.tournaments.edit',
            'update' => 'federations.tournaments.update',
            'destroy' => 'federations.tournaments.destroy',
        ]);
});

Route::prefix('tournaments/{tournament}')->group(function () {
    Route::resource('seasons', TournamentSeasonController::class)
        ->names([
            'index' => 'tournaments.seasons.index',
            'create' => 'tournaments.seasons.create',
            'store' => 'tournaments.seasons.store',
            'edit' => 'tournaments.seasons.edit',
            'update' => 'tournaments.seasons.update',
            'destroy' => 'tournaments.seasons.destroy',
        ]);
});


// Для Inertia-страниц
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

require __DIR__.'/auth.php';
