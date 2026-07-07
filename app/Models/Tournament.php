<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'federation_id',
        'name',
        'description',
        'location',
    ];

    public function federation(): BelongsTo
    {
        return $this->belongsTo(Federation::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(TournamentSeason::class);
    }
    // Добавить валидацию в модель
    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255', // Добавить валидацию
        ];
    }
}
