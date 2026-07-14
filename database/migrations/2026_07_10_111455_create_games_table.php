<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();

            // Основные связи (обязательные)
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('season_id')->constrained('tournament_seasons')->onDelete('cascade');
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('round_id')->constrained()->onDelete('cascade');

            // Команды
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');

            // Детали матча
            $table->dateTime('start_time')->nullable();
            $table->string('venue')->nullable();
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'postponed', 'cancelled'])->default('scheduled');

            $table->timestamps();

            // Уникальность: одна пара команд не может играть дважды в одном туре
            $table->unique(['round_id', 'home_team_id', 'away_team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
