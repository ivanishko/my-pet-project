<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Round;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Обновление игры (счет, время, статус)
     */
    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,postponed,cancelled',
        ]);

        if ($validated['status'] === 'completed' &&
            ($validated['home_score'] === null || $validated['away_score'] === null)) {
            return back()->withErrors(['score' => 'Для завершения игры укажите счет']);
        }

        $game->update($validated);

        return redirect()
            ->back()
            ->with('status', 'Игра успешно обновлена');
    }

    /**
     * Массовое обновление игр тура
     */
    public function updateMultiple(Request $request, Round $round)
    {
        $validated = $request->validate([
            'games' => 'required|array',
            'games.*.id' => 'required|exists:games,id',
            'games.*.home_score' => 'nullable|integer|min:0',
            'games.*.away_score' => 'nullable|integer|min:0',
            'games.*.start_time' => 'nullable|date',
            'games.*.venue' => 'nullable|string|max:255',
            'games.*.status' => 'required|in:scheduled,in_progress,completed,postponed,cancelled',
        ]);

        foreach ($validated['games'] as $gameData) {
            $game = Game::find($gameData['id']);
            $game->update($gameData);
        }

        return redirect()
            ->back()
            ->with('status', 'Игры успешно обновлены');
    }
}
