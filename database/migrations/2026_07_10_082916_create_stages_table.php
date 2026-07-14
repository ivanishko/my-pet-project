<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('tournament_seasons')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['championship', 'group', 'playoff'])->default('championship');
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
