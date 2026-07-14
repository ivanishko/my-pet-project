<?php

namespace App\Http\Controllers;

use App\Models\TournamentSeason;
use App\Models\Stage;
use App\Models\Round;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller {
    public function store(Request $request)
{
    $validated = $request->validate([
        'season_id' => 'required|exists:tournament_seasons,id',
        'rounds' => 'required|array|min:1',
        'rounds.*.name' => 'required|string|max:255',
        'rounds.*.games' => 'required|array|min:1',
        'rounds.*.games.*.home_team_id' => 'required|exists:teams,id|different:rounds.*.games.*.away_team_id',
        'rounds.*.games.*.away_team_id' => 'required|exists:teams,id|different:rounds.*.games.*.home_team_id',
        'rounds.*.games.*.start_time' => 'nullable|date',
        'rounds.*.games.*.venue' => 'nullable|string|max:255',
        'rounds.*.games.*.home_score' => 'nullable|integer|min:0',
        'rounds.*.games.*.away_score' => 'nullable|integer|min:0',
    ]);

    $season = TournamentSeason::with('tournament')->find($validated['season_id']);

    // Находим или создаем этап "Чемпионат"
    $stage = $season->stages()->firstOrCreate(
        ['type' => 'championship'],
        [
            'name' => 'Чемпионат',
            'type' => 'championship',
            'order' => 0,
            'status' => 'upcoming',
        ]
    );

    DB::transaction(function () use ($stage, $season, $validated) {
        foreach ($validated['rounds'] as $roundData) {
            $round = Round::create([
                'stage_id' => $stage->id,
                'name' => $roundData['name'],
                'order' => Round::where('stage_id', $stage->id)->max('order') + 1,
                'type' => 'group',
                'status' => 'upcoming',
            ]);

            foreach ($roundData['games'] as $gameData) {
                $round->games()->create([
                    'tournament_id' => $season->tournament_id,
                    'season_id' => $season->id,
                    'stage_id' => $stage->id,
                    'round_id' => $round->id,
                    'home_team_id' => $gameData['home_team_id'],
                    'away_team_id' => $gameData['away_team_id'],
                    'start_time' => $gameData['start_time'],
                    'venue' => $gameData['venue'],
                    'home_score' => $gameData['home_score'],
                    'away_score' => $gameData['away_score'],
                    'status' => 'scheduled',
                ]);
            }
        }
    });

    return redirect()
        ->route('seasons.show', $validated['season_id'])
        ->with('status', 'Расписание успешно сохранено');
}
}
