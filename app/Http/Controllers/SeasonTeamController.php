<?php

namespace App\Http\Controllers;

use App\Models\TournamentSeason;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeasonTeamController extends Controller
{
    /**
     * Показать список команд в сезоне
     */
    public function index(TournamentSeason $season)
    {
        $federationId = $season->tournament->federation_id;

        $teams = $season->teams()->with('federation')->get();
        $teamIds = $teams->pluck('id')->toArray();

        $allTeams = Team::with('federation')
            ->where('status', 'active')
            ->orderByRaw("CASE WHEN federation_id = ? THEN 0 ELSE 1 END", [$federationId])
            ->orderBy('name')
            ->get();

        return Inertia::render('Seasons/Teams', [
            'season' => $season,
            'teams' => $teams,
            'allTeams' => $allTeams,
            'federationId' => $federationId,
            'status' => session('status'),
        ]);
    }

    /**
     * Добавить команду в сезон
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:tournament_seasons,id',
            'team_id' => 'required|exists:teams,id',
        ]);

        $season = TournamentSeason::find($validated['season_id']);

        // Проверяем, не добавлена ли уже команда
        if ($season->teams()->where('team_id', $validated['team_id'])->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Команда уже добавлена в этот сезон');
        }

        $season->teams()->attach($validated['team_id']);

        return redirect()
            ->route('seasons.teams.index', $validated['season_id'])
            ->with('status', 'Команда успешно добавлена в сезон');
    }

    /**
     * Удалить команду из сезона
     */
    public function destroy(TournamentSeason $season, Team $team)
    {
        $season->teams()->detach($team->id);

        return redirect()
            ->route('seasons.teams.index', $season->id)
            ->with('status', 'Команда удалена из сезона');
    }

    /**
     * Добавить несколько команд сразу
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:tournament_seasons,id',
            'team_ids' => 'required|array|min:1',
            'team_ids.*' => 'exists:teams,id',
        ]);

        $season = TournamentSeason::find($validated['season_id']);

        $added = 0;
        foreach ($validated['team_ids'] as $teamId) {
            if (!$season->teams()->where('team_id', $teamId)->exists()) {
                $season->teams()->attach($teamId);
                $added++;
            }
        }

        return redirect()
            ->route('seasons.teams.index', $validated['season_id'])
            ->with('status', "Добавлено {$added} команд в сезон");
    }
}
