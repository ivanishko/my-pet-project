<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'federation_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location'
    ];

    public function federation()
    {
        return $this->belongsTo(Federation::class);
    }
}
