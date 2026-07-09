<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Federation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentController extends Controller
{
    /**
     * Список всех турниров (публичный)
     */
    public function index()
    {
        $tournaments = Tournament::with('federation')
            ->latest()
            ->get();

        return Inertia::render('Tournaments/Index', [
            'tournaments' => $tournaments,
            'status' => session('status'),
        ]);
    }

    /**
     * Форма создания турнира
     */
    public function create()
    {
        // Получаем все федерации для выпадающего списка
        $federations = Federation::orderBy('name')->get();

        return Inertia::render('Tournaments/Create', [
            'federations' => $federations,
            'status' => session('status'),
        ]);
    }

    /**
     * Сохранение нового турнира
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'federation_id' => 'required|exists:federations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'type' => 'required|in:individual,team,mixed',
            'status' => 'required|in:active,inactive,completed',
            'image' => 'nullable|image|max:2048',
        ]);

        $tournament = Tournament::create($validated);

        return redirect()
            ->route('tournaments.index')
            ->with('status', 'Турнир успешно создан');
    }

    /**
     * Просмотр конкретного турнира
     */
    public function show(Tournament $tournament)
    {
        $tournament->load(['federation', 'seasons']);

        return Inertia::render('Tournaments/Show', [
            'tournament' => $tournament,
            'seasonsCount' => $tournament->seasons()->count(),
            'status' => session('status'),
        ]);
    }

    /**
     * Форма редактирования турнира
     */
    public function edit(Tournament $tournament)
    {
        $federations = Federation::orderBy('name')->get();

        return Inertia::render('Tournaments/Edit', [
            'tournament' => $tournament,
            'federations' => $federations,
            'status' => session('status'),
        ]);
    }

    /**
     * Обновление турнира
     */
    public function update(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'federation_id' => 'required|exists:federations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'type' => 'required|in:individual,team,mixed',
            'status' => 'required|in:active,inactive,completed',
            'image' => 'nullable|image|max:2048',
        ]);

        $tournament->update($validated);

        return redirect()
            ->route('tournaments.index')
            ->with('status', 'Турнир успешно обновлен');
    }

    /**
     * Удаление турнира
     */
    public function destroy(Tournament $tournament)
    {
        // Проверяем, есть ли у турнира сезоны
        if ($tournament->seasons()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Нельзя удалить турнир, у которого есть сезоны');
        }

        $tournament->delete();

        return redirect()
            ->route('tournaments.index')
            ->with('status', 'Турнир успешно удален');
    }
}
