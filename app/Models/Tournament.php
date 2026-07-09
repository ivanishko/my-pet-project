<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'federation_id',
        'name',         // Обратите внимание! В вашей таблице поле называется name, а не title
        'description',
        'location',
        'type',
        'status',
        'image',
    ];

    /**
     * Связь с федерацией
     */
    public function federation()
    {
        return $this->belongsTo(Federation::class);
    }

    /**
     * Связь с сезонами
     */
    public function seasons()
    {
        return $this->hasMany(TournamentSeason::class);
    }

    /**
     * Получить текущий активный сезон
     */
    public function activeSeason()
    {
        return $this->hasOne(TournamentSeason::class)->where('status', 'active');
    }

    /**
     * Получить количество сезонов
     */
    public function getSeasonsCountAttribute()
    {
        return $this->seasons()->count();
    }

    /**
     * Типы турниров
     */
    public static function getTypes()
    {
        return [
            'individual' => 'Индивидуальный',
            'team' => 'Командный',
            'mixed' => 'Смешанный',
        ];
    }

    /**
     * Статусы турниров
     */
    public static function getStatuses()
    {
        return [
            'active' => 'Активный',
            'inactive' => 'Неактивный',
            'completed' => 'Завершен',
        ];
    }
}
