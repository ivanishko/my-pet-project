<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TournamentSeason extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'year',
        'is_current', // если хотите оставить
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Связь с турниром
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Автоматическое создание названия на основе дат
     */
    public static function generateName($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $startYear = $start->year;
        $endYear = $end->year;

        if ($startYear === $endYear) {
            return (string) $startYear;
        }

        return $startYear . ' - ' . $endYear;
    }

    /**
     * Проверка валидности дат
     */
    public static function validateDates($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->lt($start)) {
            return 'Дата окончания не может быть раньше даты начала';
        }

        $diffInDays = $start->diffInDays($end);
        if ($diffInDays > 366) {
            return 'Сезон не может длиться более одного года';
        }

        return null;
    }

    /**
     * Получить статус сезона на основе дат
     */
    public function getCalculatedStatus()
    {
        $now = Carbon::now();
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        if ($now->lt($start)) {
            return 'upcoming';
        } elseif ($now->between($start, $end)) {
            return 'active';
        } else {
            return 'completed';
        }
    }

    /**
     * Обновить статус сезона
     */
    public function updateStatus()
    {
        $newStatus = $this->getCalculatedStatus();
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
        return $this;
    }

    /**
     * Проверить, является ли сезон текущим
     */
    public function isCurrent()
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, 'season_id')->orderBy('order');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'season_teams', 'season_id', 'team_id')
            ->withTimestamps()
            ->orderBy('teams.name');
    }

    public function getTeamsCountAttribute()
    {
        return $this->teams()->count();
    }
}
