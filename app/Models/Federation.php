<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Federation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'website',
        'email',
        'phone',
    ];

    /**
     * Связь с турнирами
     */
    public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }

    /**
     * Связь с сезонами через турниры
     */
    public function seasons()
    {
        return $this->hasManyThrough(TournamentSeason::class, Tournament::class);
    }

    /**
     * Получить количество турниров
     */
    public function getTournamentsCountAttribute()
    {
        return $this->tournaments()->count();
    }
    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }
}
