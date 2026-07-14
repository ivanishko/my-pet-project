<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'name',
        'order',
        'type',
        'description',
        'status',
    ];

    /**
     * Связь с этапом
     */
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Связь с играми
     */
    public function games()
    {
        return $this->hasMany(Game::class)->orderBy('id');
    }

    /**
     * Получить количество игр
     */
    public function getGamesCountAttribute()
    {
        return $this->games()->count();
    }

    /**
     * Получить названия типов туров
     */
    public static function getTypes()
    {
        return [
            'group' => 'Групповой тур',
            'playoff' => 'Плей-офф раунд',
        ];
    }

    /**
     * Получить названия статусов
     */
    public static function getStatuses()
    {
        return [
            'upcoming' => 'Предстоящий',
            'active' => 'Активный',
            'completed' => 'Завершен',
        ];
    }

    /**
     * Сгенерировать туры для кругового этапа (каждый с каждым)
     */
    public static function generateRoundRobinRounds($stageId, $teamIds)
    {
        $rounds = [];
        $teamCount = count($teamIds);

        // Для нечетного количества команд добавляем "виртуальную" команду (bye)
        $teams = $teamIds;
        if ($teamCount % 2 != 0) {
            $teams[] = null; // bye
            $teamCount++;
        }

        $totalRounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;

        // Алгоритм кругового турнира
        for ($round = 0; $round < $totalRounds; $round++) {
            $roundMatches = [];
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = $teams[$match];
                $away = $teams[$teamCount - 1 - $match];

                // Пропускаем bye-матчи
                if ($home !== null && $away !== null) {
                    $roundMatches[] = [
                        'home_team_id' => $home,
                        'away_team_id' => $away,
                    ];
                }
            }

            // Сдвигаем команды для следующего тура (кроме первой)
            $teams = array_merge(
                [$teams[0]],
                [$teams[$teamCount - 1]],
                array_slice($teams, 1, $teamCount - 2)
            );

            $rounds[] = [
                'name' => 'Тур ' . ($round + 1),
                'order' => $round,
                'matches' => $roundMatches,
            ];
        }

        return $rounds;
    }
}
