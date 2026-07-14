<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('season_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('tournament_seasons')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['season_id', 'team_id']); // Одна команда не может быть добавлена дважды
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_teams');
    }
};
