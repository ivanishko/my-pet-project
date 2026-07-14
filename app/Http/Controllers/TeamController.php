<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Federation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    /**
     * Список всех команд
     */
    public function index()
    {
        $teams = Team::with('federation')
            ->latest()
            ->get();

        $federations = Federation::orderBy('name')->get();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'federations' => $federations,
            'status' => session('status'),
        ]);
    }

    /**
     * Список команд федерации
     */
    public function indexByFederation(Federation $federation)
    {
        $teams = $federation->teams()
            ->latest()
            ->get();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'federation' => $federation,
            'federations' => [$federation],
            'status' => session('status'),
        ]);
    }

    /**
     * Просмотр команды
     */
    public function show(Team $team)
    {
        $team->load('federation');

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'status' => session('status'),
        ]);
    }

    /**
     * Сохранение команды (через модальное окно)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'federation_id' => 'required|exists:federations,id',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team = Team::create($validated);

        return redirect()
            ->route('teams.index')
            ->with('status', 'Команда успешно создана');
    }

    /**
     * Редактирование команды
     */
    public function edit(Team $team)
    {
        $federations = Federation::orderBy('name')->get();

        return Inertia::render('Teams/Edit', [
            'team' => $team,
            'federations' => $federations,
            'status' => session('status'),
        ]);
    }

    /**
     * Обновление команды
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'federation_id' => 'required|exists:federations,id',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|image|max:2048',
        ]);

        $team->update($validated);

        return redirect()
            ->route('teams.index')
            ->with('status', 'Команда успешно обновлена');
    }

    /**
     * Удаление команды
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()
            ->route('teams.index')
            ->with('status', 'Команда успешно удалена');
    }
}
