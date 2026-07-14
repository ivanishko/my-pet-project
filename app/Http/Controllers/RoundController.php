<?php

namespace App\Http\Controllers;

use App\Models\Round;
use App\Models\Stage;
use App\Models\Team;
use App\Models\Game;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoundController extends Controller
{
    /**
     * Список туров этапа
     */
    public function index(Stage $stage)
    {
        $rounds = $stage->rounds()->with('games.homeTeam', 'games.awayTeam')->get();

        return Inertia::render('Rounds/Index', [
            'stage' => $stage,
            'rounds' => $rounds,
            'status' => session('status'),
        ]);
    }

    /**
     * Создание тура
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:group,playoff',
            'description' => 'nullable|string',
        ]);

        $maxOrder = Round::where('stage_id', $validated['stage_id'])->max('order') ?? -1;
        $validated['order'] = $maxOrder + 1;
        $validated['status'] = 'upcoming';

        Round::create($validated);

        return redirect()
            ->route('stages.show', $validated['stage_id'])
            ->with('status', 'Тур успешно создан');
    }

    /**
     * Генерация расписания для этапа
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:stages,id',
        ]);

        $stage = Stage::with(['season.teams'])->find($validated['stage_id']);

        if (!$stage) {
            return redirect()
                ->back()
                ->with('error', 'Этап не найден');
        }

        $teams = $stage->season->teams;
        $teamIds = $teams->pluck('id')->toArray();

        if (count($teamIds) < 2) {
            return redirect()
                ->back()
                ->with('error', 'Для генерации расписания нужно минимум 2 команды');
        }

        // Удаляем существующие туры и игры
        $stage->rounds()->each(function ($round) {
            $round->games()->delete();
            $round->delete();
        });

        // Генерируем туры в зависимости от типа этапа
        if ($stage->type === 'championship') {
            $this->generateChampionshipSchedule($stage, $teamIds);
        } elseif ($stage->type === 'group') {
            $this->generateGroupSchedule($stage, $teamIds);
        } elseif ($stage->type === 'playoff') {
            $this->generatePlayoffSchedule($stage, $teamIds);
        }

        return redirect()
            ->route('stages.show', $stage->id)
            ->with('status', 'Расписание успешно сгенерировано');
    }

    /**
     * Генерация расписания для чемпионата (круговой турнир)
     */
    private function generateChampionshipSchedule($stage, $teamIds)
    {
        $roundsData = Round::generateRoundRobinRounds($stage->id, $teamIds);

        foreach ($roundsData as $roundData) {
            $round = Round::create([
                'stage_id' => $stage->id,
                'name' => $roundData['name'],
                'order' => $roundData['order'],
                'type' => 'group',
                'status' => 'upcoming',
            ]);

            foreach ($roundData['matches'] as $gameData) {
                $round->games()->create([
                    'home_team_id' => $gameData['home_team_id'],
                    'away_team_id' => $gameData['away_team_id'],
                    'status' => 'scheduled',
                ]);
            }
        }
    }

    /**
     * Генерация расписания для группового этапа
     */
    private function generateGroupSchedule($stage, $teamIds)
    {
        // Для простоты используем круговой турнир
        // В будущем можно добавить разбивку на группы
        $this->generateChampionshipSchedule($stage, $teamIds);
    }

    /**
     * Генерация расписания для плей-офф
     */
    private function generatePlayoffSchedule($stage, $teamIds)
    {
        $count = count($teamIds);
        $stageNames = $this->getPlayoffStageNames($count);

        // Перемешиваем команды для случайной жеребьевки
        shuffle($teamIds);

        $roundNumber = 0;
        foreach ($stageNames as $stageName) {
            $round = Round::create([
                'stage_id' => $stage->id,
                'name' => $stageName,
                'order' => $roundNumber,
                'type' => 'playoff',
                'status' => 'upcoming',
            ]);

            $teamsCount = count($teamIds);
            for ($i = 0; $i < $teamsCount; $i += 2) {
                if (isset($teamIds[$i + 1])) {
                    $round->games()->create([
                        'home_team_id' => $teamIds[$i],
                        'away_team_id' => $teamIds[$i + 1],
                        'status' => 'scheduled',
                    ]);
                }
            }

            // Переход к следующему раунду (победители)
            $teamIds = array_chunk($teamIds, 2);
            $teamIds = array_map(function($pair) {
                return $pair[0] ?? null;
            }, $teamIds);
            $teamIds = array_filter($teamIds);

            $roundNumber++;
        }
    }

    /**
     * Получить названия стадий плей-офф в зависимости от количества команд
     */
    private function getPlayoffStageNames($teamCount)
    {
        $stages = [];

        if ($teamCount >= 64) $stages[] = '1/64 финала';
        if ($teamCount >= 32) $stages[] = '1/32 финала';
        if ($teamCount >= 16) $stages[] = '1/16 финала';
        if ($teamCount >= 8) $stages[] = '1/8 финала';
        if ($teamCount >= 4) $stages[] = '1/4 финала';
        if ($teamCount >= 2) {
            $stages[] = 'Полуфинал';
            $stages[] = 'Финал';
        }

        return $stages;
    }

    /**
     * Обновление тура
     */
    public function update(Request $request, Round $round)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        $round->update($validated);

        return redirect()
            ->route('stages.show', $round->stage_id)
            ->with('status', 'Тур успешно обновлен');
    }

    /**
     * Удаление тура
     */
    public function destroy(Round $round)
    {
        // Проверяем, есть ли в туре матчи
        if ($round->games()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Невозможно удалить тур, в котором уже есть матчи. Сначала удалите все матчи.');
        }

        $stageId = $round->stage_id;
        $round->delete();

        return redirect()
            ->route('stages.show', $stageId)
            ->with('status', 'Тур успешно удален');
    }


    /**
     * Добавление игры в тур
     */
    public function addGame(Request $request, Round $round)
    {
        $validated = $request->validate([
            'home_team_id' => 'required|exists:teams,id|different:away_team_id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'start_time' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,postponed,cancelled',
        ]);

        // Проверка, что команды не играют уже в этом туре
        $exists = $round->games()
            ->where(function($query) use ($validated) {
                $query->where('home_team_id', $validated['home_team_id'])
                      ->where('away_team_id', $validated['away_team_id']);
            })
            ->orWhere(function($query) use ($validated) {
                $query->where('home_team_id', $validated['away_team_id'])
                      ->where('away_team_id', $validated['home_team_id']);
            })
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'Эти команды уже играют в этом туре');
        }

        $game = $round->games()->create($validated);

        return redirect()
            ->route('stages.show', $round->stage_id)
            ->with('status', 'Игра успешно добавлена');
    }

    /**
     * Создание тура из страницы сезона
     */
    public function storeFromSeason(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:tournament_seasons,id',
            'name' => 'required|string|max:255',
        ]);

        $season = TournamentSeason::find($validated['season_id']);

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

        // Создаем тур
        $maxOrder = Round::where('stage_id', $stage->id)->max('order') ?? -1;

        $round = Round::create([
            'stage_id' => $stage->id,
            'name' => $validated['name'],
            'order' => $maxOrder + 1,
            'type' => 'group',
            'status' => 'upcoming',
        ]);

        return redirect()
            ->route('seasons.show', $validated['season_id'])
            ->with('status', 'Тур "' . $round->name . '" успешно создан');
    }

    /**
     * Создание тура с матчами
     */
    public function storeWithGames(Request $request)
    {
        $validated = $request->validate([
            'season_id' => 'required|exists:tournament_seasons,id',
            'name' => 'required|string|max:255',
            'games' => 'required|array|min:1',
            'games.*.home_team_id' => 'required|exists:teams,id|different:games.*.away_team_id',
            'games.*.away_team_id' => 'required|exists:teams,id|different:games.*.home_team_id',
            'games.*.start_time' => 'nullable|date',
            'games.*.venue' => 'nullable|string|max:255',
            'games.*.home_score' => 'nullable|integer|min:0',
            'games.*.away_score' => 'nullable|integer|min:0',
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

        // Создаем тур
        $maxOrder = Round::where('stage_id', $stage->id)->max('order') ?? -1;

        $round = Round::create([
            'stage_id' => $stage->id,
            'name' => $validated['name'],
            'order' => $maxOrder + 1,
            'type' => 'group',
            'status' => 'upcoming',
        ]);

        // Создаем матчи
        foreach ($validated['games'] as $gameData) {
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

        return redirect()
            ->route('seasons.show', $validated['season_id'])
            ->with('status', 'Тур "' . $round->name . '" успешно создан с ' . count($validated['games']) . ' матчами');
    }
}
