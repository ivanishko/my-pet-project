<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TournamentSeason extends Model
{
    protected $fillable = [
        'tournament_id',
        'name',
        'start_date',
        'end_date',
        'is_current'
    ];

    protected static function booted()
    {
        static::creating(function ($season) {
            $startYear = Carbon::parse($season->start_date)->year;
            $endYear = Carbon::parse($season->end_date)->year;

            $season->name = $startYear == $endYear
                ? $startYear
                : $startYear.'-'.$endYear;
        });
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
