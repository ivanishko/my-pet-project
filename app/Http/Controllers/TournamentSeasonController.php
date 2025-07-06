<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentSeasonController extends Controller
{
    public function index(Tournament $tournament)
    {
        return Inertia::render('Tournaments/Seasons/Index', [
            'tournament' => $tournament->load('seasons'),
            'status' => session('status'),
        ]);
    }

    public function create(Tournament $tournament)
    {
        return Inertia::render('Tournaments/Seasons/Create', [
            'tournament' => $tournament,
        ]);
    }

    public function store(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_current' => 'boolean',
        ]);

        // Деактивируем текущий сезон если нужно
        if ($request->is_current) {
            $tournament->seasons()->update(['is_current' => false]);
        }

        $tournament->seasons()->create($validated);

        return redirect()
            ->route('tournaments.seasons.index', $tournament->id)
            ->with('status', 'Сезон успешно создан');
    }

    // ... остальные методы (edit, update, destroy)
}
