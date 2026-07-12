<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'name',
        'type',
        'order',
        'description',
        'status',
    ];

    public function season()
    {
        return $this->belongsTo(TournamentSeason::class, 'season_id');
    }

    /**
     * Типы этапов
     */
    public static function getTypes()
    {
        return [
            'championship' => 'Чемпионат',
            'group' => 'Групповой этап',
            'playoff' => 'Плей-офф',
        ];
    }

    /**
     * Получить название типа на русском
     */
    public function getTypeNameAttribute()
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Автоматическое создание названия на основе типа
     */
    public static function generateName($type)
    {
        $types = self::getTypes();
        return $types[$type] ?? $type;
    }
    public function rounds()
    {
        return $this->hasMany(Round::class)->orderBy('order');
    }

    public function getRoundsCountAttribute()
    {
        return $this->rounds()->count();
    }
}
