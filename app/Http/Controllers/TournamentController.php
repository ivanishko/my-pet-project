<?php

namespace App\Http\Controllers;

use App\Models\Federation;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public function index(Federation $federation)
    {
        return Inertia::render('Federations/Tournaments/Index', [
            'federation' => $federation,
            'tournaments' => $federation->tournaments()->latest()->get(),
            'status' => session('status'),
        ]);
    }

    public function create(Federation $federation)
    {
        return Inertia::render('Federations/Tournaments/Create', [
            'federation' => $federation,
        ]);
    }

    public function store(Request $request, Federation $federation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
        ]);

        $federation->tournaments()->create($validated);

        return redirect()
            ->route('federations.tournaments.index', $federation->id)
            ->with('status', 'Турнир успешно создан');
    }

    public function edit(Federation $federation, Tournament $tournament)
    {
        return Inertia::render('Federations/Tournaments/Edit', [
            'federation' => $federation,
            'tournament' => $tournament,
        ]);
    }

    public function update(Request $request, Federation $federation, Tournament $tournament)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
        ]);

        $tournament->update($validated);

        return redirect()
            ->route('federations.tournaments.index', $federation->id)
            ->with('status', 'Турнир успешно обновлен');
    }

    public function destroy(Federation $federation, Tournament $tournament)
    {
        $tournament->delete();

        return redirect()
            ->route('federations.tournaments.index', $federation->id)
            ->with('status', 'Турнир успешно удален');
    }
}
