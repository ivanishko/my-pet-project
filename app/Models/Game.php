<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'season_id',
        'stage_id',
        'round_id',
        'home_team_id',
        'away_team_id',
        'start_time',
        'venue',
        'home_score',
        'away_score',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    // Связи
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function season()
    {
        return $this->belongsTo(TournamentSeason::class, 'season_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    // Статусы
    public static function getStatuses()
    {
        return [
            'scheduled' => 'Запланирована',
            'in_progress' => 'В процессе',
            'completed' => 'Завершена',
            'postponed' => 'Перенесена',
            'cancelled' => 'Отменена',
        ];
    }

    public function getScoreAttribute()
    {
        if ($this->home_score !== null && $this->away_score !== null) {
            return $this->home_score . ' - ' . $this->away_score;
        }
        return null;
    }
}
