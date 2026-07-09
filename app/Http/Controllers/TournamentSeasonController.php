<?php

namespace App\Http\Controllers;

use App\Models\TournamentSeason;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TournamentSeasonController extends Controller
{
    /**
     * Список всех сезонов показываем только активные
     */
    public function index()
    {
        $seasons = TournamentSeason::with(['tournament.federation'])
            ->whereIn('status', ['upcoming', 'active']) // Только предстоящие и активные
            ->latest()
            ->get();

        return Inertia::render('Seasons/Index', [
            'seasons' => $seasons,
            'status' => session('status'),
        ]);
    }
    // Все сезоны
    public function all()
    {
        $seasons = TournamentSeason::with(['tournament.federation'])
            ->orderBy('year', 'desc')
            ->get();

        return Inertia::render('Seasons/Index', [
            'seasons' => $seasons,
            'status' => session('status'),
        ]);
    }

    /**
     * Создание сезона (через модальное окно)
     */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'tournament_id' => 'required|exists:tournaments,id',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Проверка на длительность более 1 года
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);

            if ($start->diffInDays($end) > 366) {
                return back()->withErrors([
                    'end_date' => 'Сезон не может длиться более одного года'
                ]);
            }

            // Генерация названия
            $name = TournamentSeason::generateName(
                $validated['start_date'],
                $validated['end_date']
            );

            // Определение статуса
            $now = Carbon::now();
            $status = 'upcoming';
            if ($now->between($start, $end)) {
                $status = 'active';
            } elseif ($now->gt($end)) {
                $status = 'completed';
            }

            $season = TournamentSeason::create([
                'tournament_id' => $validated['tournament_id'],
                'name' => $name,
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $status,
                'year' => $start->year,
                'is_current' => $status === 'active', // устанавливаем is_current на основе статуса
            ]);

            return redirect()
                ->route('tournaments.show', $validated['tournament_id'])
                ->with('status', 'Сезон успешно создан');
        }

    /**
     * Просмотр сезона
     */
        public function show(TournamentSeason $season)
        {
            // Загружаем связи с турниром и федерацией
            $season->load(['tournament.federation']);

            return Inertia::render('Seasons/Show', [
                'season' => $season,
                'status' => session('status'),
            ]);
        }

    /**
     * Редактирование сезона
     */
    public function edit(TournamentSeason $season)
    {
        $tournaments = Tournament::with('federation')
            ->orderBy('name')
            ->get();

        // Преобразуем даты в формат YYYY-MM-DD для input type="date"
        $season->start_date = $season->start_date ? \Carbon\Carbon::parse($season->start_date)->format('Y-m-d') : null;
        $season->end_date = $season->end_date ? \Carbon\Carbon::parse($season->end_date)->format('Y-m-d') : null;

        return Inertia::render('Seasons/Edit', [
            'season' => $season,
            'tournaments' => $tournaments,
            'status' => session('status'),
        ]);
    }

    /**
     * Обновление сезона
     */
    public function update(Request $request, TournamentSeason $season)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Проверка на длительность более 1 года
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        if ($start->diffInDays($end) > 366) {
            return back()->withErrors([
                'end_date' => 'Сезон не может длиться более одного года'
            ]);
        }

        // Генерация названия
        $name = TournamentSeason::generateName(
            $validated['start_date'],
            $validated['end_date']
        );

        // Определение статуса
        $now = Carbon::now();
        $status = 'upcoming';
        if ($now->between($start, $end)) {
            $status = 'active';
        } elseif ($now->gt($end)) {
            $status = 'completed';
        }

        $season->update([
            'tournament_id' => $validated['tournament_id'],
            'name' => $name,
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $status,
            'year' => $start->year,
        ]);

        return redirect()
            ->route('tournaments.show', $validated['tournament_id'])
            ->with('status', 'Сезон успешно обновлен');
    }

    /**
     * Удаление сезона
     */
    public function destroy(TournamentSeason $season)
    {
        $tournamentId = $season->tournament_id;
        $season->delete();

        return redirect()
            ->route('tournaments.show', $tournamentId)
            ->with('status', 'Сезон успешно удален');
    }
}
