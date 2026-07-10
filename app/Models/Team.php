<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'federation_id',
        'name',
        'city',
        'logo',
        'description',
        'status',
    ];

    /**
     * Связь с федерацией
     */
    public function federation()
    {
        return $this->belongsTo(Federation::class);
    }

    /**
     * Связь с турнирами (через турнирные таблицы)
     */
    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_teams');
    }

    /**
     * Получить URL логотипа
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function seasons()
    {
        return $this->belongsToMany(TournamentSeason::class, 'season_teams', 'team_id', 'season_id')
            ->withTimestamps();
    }
}
